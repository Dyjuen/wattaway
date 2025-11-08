<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeviceReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'firmware_version',
        'timestamp',
        'voltage',
        'voltage_raw',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'voltage' => 'float',
        'voltage_raw' => 'integer',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function channelReadings(): HasMany
    {
        return $this->hasMany(ChannelReading::class);
    }
}
