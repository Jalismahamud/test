<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'sale_id', 'product_id', 'product_name', 'product_sku',
        'quantity', 'unit_price', 'cost_price', 'discount', 'tax_amount', 'total',
    ];

    protected $casts = [
        'quantity'   => 'decimal:3',
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'discount'   => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total'      => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
