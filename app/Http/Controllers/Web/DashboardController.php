<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $businessId = auth()->user()->business_id;

        $todaySales = Sale::whereDate('sold_at', today())
            ->where('status', 'completed')
            ->count();

        $todayRevenue = Sale::whereDate('sold_at', today())
            ->where('status', 'completed')
            ->sum('total_amount');

        $lowStockCount = Product::active()->lowStock()->count();
        $totalProducts = Product::active()->count();

        $recentSales = Sale::with(['cashier:id,name', 'items'])
            ->completed()
            ->latest('sold_at')
            ->take(10)
            ->get()
            ->map(fn($sale) => [
                'id'             => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'total_amount'   => $sale->total_amount,
                'payment_method' => $sale->payment_method,
                'cashier'        => $sale->cashier?->name,
                'items_count'    => $sale->items->count(),
                'sold_at'        => $sale->sold_at->format('d M Y, h:i A'),
            ]);

        return Inertia::render('Dashboard/Index', [
            'stats' => [
                'todaySales'    => $todaySales,
                'todayRevenue'  => number_format((float) $todayRevenue, 2),
                'lowStockCount' => $lowStockCount,
                'totalProducts' => $totalProducts,
            ],
            'recentSales' => $recentSales,
        ]);
    }
}
