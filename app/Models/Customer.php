<?php

namespace App\Models;

use App\Traits\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use BelongsToBusiness;

    protected $fillable = [
        'business_id', 'name', 'phone', 'email', 'address', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
