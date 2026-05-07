<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Services\POS\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    public function __construct(private SaleService $saleService) {}

    public function push(Request $request): JsonResponse
    {
        $request->validate([
            'items'             => 'required|array',
            'items.*.uuid'      => 'required|uuid',
            'items.*.type'      => 'required|in:sale',
            'items.*.payload'   => 'required|array',
        ]);

        $results = [];

        foreach ($request->items as $item) {
            $existing = Sale::withoutBusinessScope()->where('uuid', $item['uuid'])->first();

            if ($existing) {
                $results[] = ['uuid' => $item['uuid'], 'status' => 'synced', 'server_id' => $existing->id];
                continue;
            }

            try {
                $sale = DB::transaction(fn() =>
                    $this->saleService->processSale($item['payload'], auth()->user())
                );
                $results[] = ['uuid' => $item['uuid'], 'status' => 'synced', 'server_id' => $sale->id];
            } catch (\Exception $e) {
                $results[] = ['uuid' => $item['uuid'], 'status' => 'failed', 'error' => $e->getMessage()];
            }
        }

        return response()->json(['success' => true, 'results' => $results]);
    }

    public function pull(Request $request): JsonResponse
    {
        $since = $request->query('since');

        $products = Product::with(['category:id,name'])
            ->when($since, fn($q) => $q->where('updated_at', '>', $since))
            ->get();

        $categories = Category::active()
            ->when($since, fn($q) => $q->where('updated_at', '>', $since))
            ->get();

        return response()->json([
            'success'    => true,
            'products'   => $products,
            'categories' => $categories,
            'pulled_at'  => now()->toISOString(),
        ]);
    }

    public function status(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'server_time' => now()->toISOString(),
                'status'      => 'online',
            ],
        ]);
    }
}
