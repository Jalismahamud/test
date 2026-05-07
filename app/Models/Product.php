<?php

namespace App\Models;

use App\Traits\BelongsToBusiness;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasUuid, BelongsToBusiness;

    protected $fillable = [
        'uuid', 'business_id', 'category_id', 'brand_id', 'name', 'sku',
        'barcode', 'description', 'cost_price', 'selling_price', 'tax_rate',
        'unit', 'image', 'is_active', 'track_inventory', 'alert_quantity',
    ];

    protected $casts = [
        'cost_price'      => 'decimal:2',
        'selling_price'   => 'decimal:2',
        'tax_rate'        => 'decimal:2',
        'is_active'       => 'boolean',
        'track_inventory' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getCurrentStockAttribute(): float
    {
        return (float) StockMovement::where('product_id', $this->id)
            ->selectRaw('SUM(CASE WHEN type = "in" THEN quantity ELSE -quantity END) as total')
            ->value('total') ?? 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('alert_quantity >= (
            SELECT COALESCE(SUM(CASE WHEN type = "in" THEN quantity ELSE -quantity END), 0)
            FROM stock_movements WHERE product_id = products.id
        )');
    }
}
