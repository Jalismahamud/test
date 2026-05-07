<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uuid'                    => 'nullable|uuid',
            'customer_id'             => 'nullable|exists:customers,id',
            'items'                   => 'required|array|min:1',
            'items.*.product_id'      => 'required|exists:products,id',
            'items.*.quantity'        => 'required|numeric|min:0.001',
            'items.*.unit_price'      => 'required|numeric|min:0',
            'items.*.discount'        => 'nullable|numeric|min:0',
            'items.*.tax_amount'      => 'nullable|numeric|min:0',
            'items.*.total'           => 'required|numeric|min:0',
            'subtotal'                => 'required|numeric|min:0',
            'tax_amount'              => 'required|numeric|min:0',
            'discount_amount'         => 'nullable|numeric|min:0',
            'total_amount'            => 'required|numeric|min:0',
            'paid_amount'             => 'required|numeric|min:0',
            'change_amount'           => 'nullable|numeric|min:0',
            'payment_method'          => 'required|in:cash,card,mobile,mixed',
            'note'                    => 'nullable|string',
            'sold_at'                 => 'nullable|date',
        ];
    }
}
