<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $products = Product::with(['category:id,name', 'brand:id,name'])
            ->when(request('search'), fn($q) => $q->where(fn($q) =>
                $q->where('name', 'like', '%'.request('search').'%')
                  ->orWhere('sku', 'like', '%'.request('search').'%')
                  ->orWhere('barcode', 'like', '%'.request('search').'%')
            ))
            ->when(request('category_id'), fn($q) => $q->where('category_id', request('category_id')))
            ->when(request('is_active') !== null, fn($q) => $q->where('is_active', request('is_active')))
            ->latest()
            ->paginate(20);

        return ProductResource::collection($products);
    }

    public function search(): JsonResponse
    {
        $products = Product::active()
            ->when(request('q'), fn($q) => $q->where(fn($q) =>
                $q->where('name', 'like', '%'.request('q').'%')
                  ->orWhere('sku', 'like', '%'.request('q').'%')
                  ->orWhere('barcode', request('q'))
            ))
            ->when(request('category_id'), fn($q) => $q->where('category_id', request('category_id')))
            ->take(15)
            ->get(['id', 'uuid', 'name', 'sku', 'barcode', 'selling_price', 'cost_price', 'tax_rate', 'unit'])
            ->map(fn($p) => array_merge($p->toArray(), ['current_stock' => $p->current_stock]));

        return response()->json(['success' => true, 'data' => $products]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return response()->json([
            'success' => true,
            'data'    => new ProductResource($product->load('category', 'brand')),
        ], 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => new ProductResource($product->load('category', 'brand')),
        ]);
    }

    public function update(StoreProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json([
            'success' => true,
            'data'    => new ProductResource($product->fresh(['category', 'brand'])),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        if ($product->stockMovements()->exists()) {
            $product->update(['is_active' => false]);
            return response()->json(['success' => true, 'message' => 'Product deactivated.']);
        }

        $product->delete();
        return response()->json(['success' => true, 'message' => 'Product deleted.']);
    }
}
