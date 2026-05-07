<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Inertia\Inertia;
use Inertia\Response;

class SaleController extends Controller
{
    public function index(): Response
    {
        $sales = Sale::with(['cashier:id,name', 'customer:id,name'])
            ->when(request('from'), fn($q) => $q->whereDate('sold_at', '>=', request('from')))
            ->when(request('to'), fn($q) => $q->whereDate('sold_at', '<=', request('to')))
            ->when(request('status'), fn($q) => $q->where('status', request('status')))
            ->latest('sold_at')
            ->paginate(20)
            ->through(fn($s) => [
                'id'             => $s->id,
                'invoice_number' => $s->invoice_number,
                'total_amount'   => $s->total_amount,
                'payment_method' => $s->payment_method,
                'status'         => $s->status,
                'cashier'        => $s->cashier?->name,
                'customer'       => $s->customer?->name ?? 'Walk-in',
                'sold_at'        => $s->sold_at->format('d M Y, h:i A'),
            ]);

        return Inertia::render('Sales/Index', [
            'sales'   => $sales,
            'filters' => request()->only(['from', 'to', 'status']),
        ]);
    }
}
