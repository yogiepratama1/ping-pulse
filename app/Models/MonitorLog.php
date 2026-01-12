<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'monitor_id',
        'status_code',
        'response_time_ms',
        'avg_response_time_ms',
        'is_success',
        'error_message',
        'region',
        'created_at'
    ];

    protected $casts = [
        'is_success' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('is_success', true);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
