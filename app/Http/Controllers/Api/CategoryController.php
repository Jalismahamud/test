<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::active()->withCount('products')->get();
        return response()->json(['success' => true, 'data' => $categories]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string|max:100']);
        $category = Category::create(['name' => $request->name, 'is_active' => true]);
        return response()->json(['success' => true, 'data' => $category], 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $request->validate(['name' => 'required|string|max:100', 'is_active' => 'boolean']);
        $category->update($request->only('name', 'is_active'));
        return response()->json(['success' => true, 'data' => $category]);
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->products()->exists()) {
            return response()->json(['success' => false, 'message' => 'Category has products.'], 422);
        }
        $category->delete();
        return response()->json(['success' => true]);
    }
}
