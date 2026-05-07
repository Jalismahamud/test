<?php

namespace App\Models;

use App\Traits\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;

class SyncQueue extends Model
{
    use BelongsToBusiness;

    protected $table = 'sync_queue';

    protected $fillable = [
        'business_id', 'user_id', 'device_id', 'entity_type',
        'entity_uuid', 'payload', 'status', 'attempts',
        'error_message', 'synced_at',
    ];

    protected $casts = [
        'payload'   => 'array',
        'synced_at' => 'datetime',
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
