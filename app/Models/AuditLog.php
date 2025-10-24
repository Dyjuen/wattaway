<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    const UPDATED_AT = null; // Only track creation time

    protected $fillable = [
        'user_id', 'device_id', 'action', 'description',
        'ip_address', 'user_agent', 'old_values', 'new_values'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public static function log(string $action, string $description, array $context = []): void
    {
        static::create([
            'user_id' => auth()->id(),
            'device_id' => $context['device_id'] ?? null,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => $context['old_values'] ?? null,
            'new_values' => $context['new_values'] ?? null,
        ]);
    }
}
