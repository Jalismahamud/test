<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\POS\SaleService;
use Illuminate\Http\JsonResponse;

class SaleController extends Controller
{
    public function __construct(private SaleService $saleService) {}

    public function index(): JsonResponse
    {
        $sales = Sale::with(['cashier:id,name', 'customer:id,name', 'items'])
            ->when(request('from'), fn($q) => $q->whereDate('sold_at', '>=', request('from')))
            ->when(request('to'), fn($q) => $q->whereDate('sold_at', '<=', request('to')))
            ->when(request('status'), fn($q) => $q->where('status', request('status')))
            ->latest('sold_at')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => SaleResource::collection($sales)]);
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        $sale = $this->saleService->processSale($request->validated(), auth()->user());

        return response()->json([
            'success' => true,
            'data'    => new SaleResource($sale),
        ], 201);
    }

    public function show(Sale $sale): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => new SaleResource($sale->load('items.product', 'cashier', 'customer')),
        ]);
    }

    public function refund(Sale $sale): JsonResponse
    {
        $sale = $this->saleService->refundSale($sale, auth()->user());

        return response()->json([
            'success' => true,
            'data'    => new SaleResource($sale),
        ]);
    }
}
