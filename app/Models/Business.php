<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = [
        'name', 'slug', 'logo', 'phone', 'email', 'address',
        'currency', 'tax_rate', 'timezone', 'settings',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'settings' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
