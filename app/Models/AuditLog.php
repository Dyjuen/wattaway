<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    const UPDATED_AT = null; // Only track creation time

    protected $fillable = [
        'account_id', 'action', 'description', 'auditable_id', 'auditable_type', 'context', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    public static function log(string $action, string $description, array $context = []): void
    {
        static::create([
            'account_id' => auth()->id(),
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
