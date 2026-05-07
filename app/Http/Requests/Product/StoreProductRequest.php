<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name'           => 'required|string|max:255',
            'sku'            => 'required|string|max:100|unique:products,sku,'.$productId,
            'category_id'    => 'required|exists:categories,id',
            'brand_id'       => 'nullable|exists:brands,id',
            'barcode'        => 'nullable|string|max:100',
            'description'    => 'nullable|string',
            'cost_price'     => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0',
            'tax_rate'       => 'nullable|numeric|between:0,100',
            'unit'           => 'nullable|string|max:20',
            'is_active'      => 'boolean',
            'track_inventory'=> 'boolean',
            'alert_quantity' => 'nullable|integer|min:0',
        ];
    }
}
