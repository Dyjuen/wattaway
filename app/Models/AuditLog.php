<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    const UPDATED_AT = null; // Only track creation time

    protected $fillable = [
        'account_id', 'action', 'description', 'auditable_id', 'auditable_type', 'context', 'ip_address', 'user_agent',
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
        $device = null;
        if (isset($context['device_id'])) {
            $device = Device::find($context['device_id']);
        }

        static::create([
            'account_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'auditable_id' => $device ? $device->id : null,
            'auditable_type' => $device ? get_class($device) : null,
            'context' => $context,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
