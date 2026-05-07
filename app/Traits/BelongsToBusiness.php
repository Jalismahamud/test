<?php

namespace App\Traits;

use App\Models\Business;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToBusiness
{
    protected static function bootBelongsToBusiness(): void
    {
        static::addGlobalScope('business', function (Builder $builder) {
            if (auth()->check() && auth()->user()->business_id) {
                $builder->where(
                    (new static)->getTable() . '.business_id',
                    auth()->user()->business_id
                );
            }
        });

        static::creating(function ($model) {
            if (empty($model->business_id) && auth()->check()) {
                $model->business_id = auth()->user()->business_id;
            }
        });
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public static function withoutBusinessScope(): Builder
    {
        return static::withoutGlobalScope('business');
    }
}
