<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $businessId = auth()->user()->business_id;

        return response()->json([
            'success' => true,
            'data'    => [
                'today_sales'   => Sale::whereDate('sold_at', today())->completed()->count(),
                'today_revenue' => Sale::whereDate('sold_at', today())->completed()->sum('total_amount'),
                'month_revenue' => Sale::whereMonth('sold_at', now()->month)->completed()->sum('total_amount'),
                'low_stock'     => Product::active()->lowStock()->count(),
                'total_products'=> Product::active()->count(),
                'recent_sales'  => Sale::with('cashier:id,name')->completed()->latest('sold_at')->take(5)->get(),
            ],
        ]);
    }

    public function sales(): JsonResponse
    {
        $from = request('from', today()->startOfMonth()->toDateString());
        $to   = request('to', today()->toDateString());

        $sales = Sale::completed()->forDateRange($from, $to);

        $topProducts = SaleItem::select('product_name', DB::raw('SUM(quantity) as qty_sold'), DB::raw('SUM(total) as revenue'))
            ->whereHas('sale', fn($q) => $q->completed()->forDateRange($from, $to))
            ->groupBy('product_name')
            ->orderByDesc('revenue')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'total_sales'    => $sales->count(),
                'total_revenue'  => $sales->sum('total_amount'),
                'total_cost'     => SaleItem::whereHas('sale', fn($q) => $q->completed()->forDateRange($from, $to))
                                    ->selectRaw('SUM(cost_price * quantity) as total')->value('total'),
                'payment_breakdown' => $sales->groupBy('payment_method')
                                    ->map(fn($g) => $g->sum('total_amount')),
                'top_products'   => $topProducts,
                'period'         => ['from' => $from, 'to' => $to],
            ],
        ]);
    }

    public function export(): JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Use /reports/sales for data.'], 501);
    }
}
