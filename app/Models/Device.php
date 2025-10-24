<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Device extends Model
{
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
}
