<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $categories = Category::withCount('products')->latest()->paginate(20);

        return Inertia::render('Inventory/Categories', [
            'categories' => $categories,
        ]);
    }
}
