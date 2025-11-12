<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DeviceProvisioningToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'serial_number',
        'hardware_id',
        'status',
        'device_id',
        'paired_by_account_id',
        'paired_at',
        'expires_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'paired_at' => 'datetime',
            'expires_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function pairedByAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'paired_by_account_id');
    }

    public function scopePending(Builder $query): void
    {
        $query->where('status', 'pending')->where(function (Builder $q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    public function scopePaired(Builder $query): void
    {
        $query->where('status', 'paired');
    }

    public function scopeExpired(Builder $query): void
    {
        $query->where('expires_at', '<', now());
    }

    public static function generate(string $serialNumber, string $hardwareId, array $metadata = []): self
    {
        return self::create([
            'token' => 'WS-'.strtoupper(Str::random(12)),
            'serial_number' => $serialNumber,
            'hardware_id' => $hardwareId,
            'status' => 'pending',
            'expires_at' => now()->addDays(30),
            'metadata' => $metadata,
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending' && ! $this->isExpired();
    }

    public function isPaired(): bool
    {
        return $this->status === 'paired';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function markAsPaired(Account $account, Device $device): void
    {
        $this->update([
            'status' => 'paired',
            'device_id' => $device->id,
            'paired_by_account_id' => $account->id,
            'paired_at' => now(),
        ]);
    }

    public function revoke(): void
    {
        $this->update(['status' => 'revoked']);
    }

    public function getQrCodeUrl(): string
    {
        return route('pairing.public-scan', ['token' => $this->token]);
    }

    public function getQrCodeData(): string
    {
        return json_encode([
            'token' => $this->token,
            'serial_number' => $this->serial_number,
            'url' => $this->getQrCodeUrl(),
        ]);
    }
}
