<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'product_id', 'business_id', 'reference_id', 'reference_type',
        'type', 'quantity', 'quantity_before', 'quantity_after',
        'note', 'created_by',
    ];

    protected $casts = [
        'quantity'        => 'decimal:3',
        'quantity_before' => 'decimal:3',
        'quantity_after'  => 'decimal:3',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
