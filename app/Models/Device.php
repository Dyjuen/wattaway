<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Device extends Model
{
    use Auditable;
    protected $fillable = [
        'account_id',
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
        ];
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
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
}
