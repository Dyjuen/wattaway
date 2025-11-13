<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Device extends Model
{
    use Auditable;
    use HasFactory;

    protected $fillable = [
        'account_id',
        'provisioning_token_id',
        'serial_number',
        'hardware_id',
        'activated_at',
        'name',
        'description',
        'status',
        'last_seen_at',
        'api_token',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($device) {
            $device->api_token = self::generateUniqueApiToken();
        });
    }

    public function regenerateApiToken()
    {
        $this->api_token = self::generateUniqueApiToken();
        $this->save();
    }

    private static function generateUniqueApiToken()
    {
        do {
            $token = Str::random(64);
        } while (self::where('api_token', $token)->exists());

        return $token;
    }

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
            'activated_at' => 'datetime',
        ];
    }

    public function getStatusAttribute($value)
    {
        if ($this->last_seen_at && $this->last_seen_at->gte(now()->subMinutes(config('wattaway.device_offline_threshold', 5)))) {
            return 'online';
        }

        return 'offline';
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function schedules()
    {
        return $this->hasMany(DeviceSchedule::class);
    }

    public function deviceReadings()
    {
        return $this->hasMany(DeviceReading::class);
    }

    public function esp32MessageLogs()
    {
        return $this->hasMany(Esp32MessageLog::class);
    }

    public function configurations()
    {
        return $this->hasMany(DeviceConfiguration::class);
    }

    public function setConfig($key, $value)
    {
        $this->configurations()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public function getConfig($key, $default = null)
    {
        $config = $this->configurations()->where('key', $key)->first();

        return $config ? $config->value : $default;
    }

    public function scopeOnline($query)
    {
        return $query->where('last_seen_at', '>=', now()->subMinutes(5));
    }

    public function scopeWithLatestData($query)
    {
        return $query->with(['esp32MessageLogs' => function ($q) {
            $q->latest()->limit(1);
        }]);
    }

    public function provisioningToken(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DeviceProvisioningToken::class);
    }

    public function isActivated(): bool
    {
        return $this->activated_at !== null;
    }
}
