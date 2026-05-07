<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class PosController extends Controller
{
    public function index(): Response
    {
        $categories = Category::active()->get(['id', 'name']);
        $business = auth()->user()->business;

        return Inertia::render('Pos/Index', [
            'categories' => $categories,
            'business'   => [
                'name'     => $business->name,
                'currency' => $business->currency,
                'tax_rate' => $business->tax_rate,
            ],
        ]);
    }
}
