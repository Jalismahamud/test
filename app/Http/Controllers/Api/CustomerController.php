<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(): JsonResponse
    {
        $customers = Customer::active()
            ->when(request('search'), fn($q) => $q->where('name', 'like', '%'.request('search').'%')
                ->orWhere('phone', 'like', '%'.request('search').'%'))
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $customers]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
        ]);

        $customer = Customer::create($request->only('name', 'phone', 'email', 'address'));
        return response()->json(['success' => true, 'data' => $customer], 201);
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        $customer->update($request->only('name', 'phone', 'email', 'address', 'is_active'));
        return response()->json(['success' => true, 'data' => $customer]);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->update(['is_active' => false]);
        return response()->json(['success' => true]);
    }
}
