<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\Incident;
use App\Models\MonitorLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Monitor extends Model
{
    use HasFactory, HasUuids;

    const STATUS_UP = 'up';
    const STATUS_DOWN = 'down';

    protected $fillable = [
        'user_id',
        'url',
        'alias',
        'check_interval',
        'status',
        'look_for_string',
        'max_retries',
        'region',
        'last_checked_at',
        'next_check_at'
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
        'next_check_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function monitorLogs()
    {
        return $this->hasMany(MonitorLog::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function scopeDueForCheck($query)
    {
        return $query->where('next_check_at', '<=', now());
    }

    public function scheduleNextCheck()
    {
        $this->update([
            'next_check_at' => now()->addSeconds($this->check_interval)
        ]);
    }

    public function getUptimePercentageAttribute()
    {
        $total = $this->monitorLogs()->where('created_at', '>=', now()->subDays(7))->count();
        if ($total === 0)
            return 100;

        $successful = $this->monitorLogs()
            ->where('created_at', '>=', now()->subDays(7))
            ->where('is_success', true)
            ->count();

        return round(($successful / $total) * 100, 2);
    }
}
