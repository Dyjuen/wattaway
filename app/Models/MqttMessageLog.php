<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MqttMessageLog extends Model
{
    const UPDATED_AT = null; // Only track creation

    protected $fillable = [
        'device_id',
        'direction',
        'type',
        'topic',
        'endpoint',
        'payload',
        'status',
        'error_message',
        'ip_address',
        'response_code',
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    // Helper method to log incoming messages
    public static function logIncoming(
        ?int $deviceId,
        string $type,
        array $payload,
        ?string $topic = null,
        ?string $endpoint = null,
        string $status = 'success',
        ?string $error = null
    ): self {
        return self::create([
            'device_id' => $deviceId,
            'direction' => 'incoming',
            'type' => $type,
            'topic' => $topic,
            'endpoint' => $endpoint,
            'payload' => $payload,
            'status' => $status,
            'error_message' => $error,
            'ip_address' => request()->ip(),
        ]);
    }

    // Helper method to log outgoing messages
    public static function logOutgoing(
        int $deviceId,
        string $type,
        array $payload,
        ?string $topic = null,
        string $status = 'success',
        ?int $responseCode = null,
        ?string $error = null
    ): self {
        return self::create([
            'device_id' => $deviceId,
            'direction' => 'outgoing',
            'type' => $type,
            'topic' => $topic,
            'payload' => $payload,
            'status' => $status,
            'response_code' => $responseCode,
            'error_message' => $error,
        ]);
    }

    // Scope to filter by direction
    public function scopeIncoming($query)
    {
        return $query->where('direction', 'incoming');
    }

    public function scopeOutgoing($query)
    {
        return $query->where('direction', 'outgoing');
    }

    // Scope to filter by type
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Scope for recent messages
    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }
}
