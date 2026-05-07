<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        $products = Product::with(['category:id,name', 'brand:id,name'])
            ->when(request('search'), fn($q) => $q->where('name', 'like', '%'.request('search').'%')
                ->orWhere('sku', 'like', '%'.request('search').'%'))
            ->when(request('category_id'), fn($q) => $q->where('category_id', request('category_id')))
            ->latest()
            ->paginate(20)
            ->through(fn($p) => [
                'id'            => $p->id,
                'name'          => $p->name,
                'sku'           => $p->sku,
                'selling_price' => $p->selling_price,
                'cost_price'    => $p->cost_price,
                'current_stock' => $p->current_stock,
                'alert_quantity'=> $p->alert_quantity,
                'is_active'     => $p->is_active,
                'category'      => $p->category?->name,
                'brand'         => $p->brand?->name,
            ]);

        return Inertia::render('Inventory/Products', [
            'products'   => $products,
            'categories' => Category::active()->get(['id', 'name']),
            'brands'     => Brand::active()->get(['id', 'name']),
            'filters'    => request()->only(['search', 'category_id']),
        ]);
    }
}
