<?php

namespace App\Models;

use App\Traits\BelongsToBusiness;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasUuid, BelongsToBusiness;

    protected $fillable = [
        'uuid', 'business_id', 'customer_id', 'cashier_id', 'invoice_number',
        'subtotal', 'tax_amount', 'discount_amount', 'total_amount',
        'paid_amount', 'change_amount', 'payment_method', 'status',
        'note', 'sold_at', 'synced_at',
    ];

    protected $casts = [
        'subtotal'         => 'decimal:2',
        'tax_amount'       => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'total_amount'     => 'decimal:2',
        'paid_amount'      => 'decimal:2',
        'change_amount'    => 'decimal:2',
        'sold_at'          => 'datetime',
        'synced_at'        => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForDateRange($query, string $from, string $to)
    {
        return $query->whereBetween('sold_at', [$from, $to]);
    }
}
