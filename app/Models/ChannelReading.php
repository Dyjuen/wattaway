<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChannelReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_reading_id',
        'channel',
        'current',
        'current_raw',
        'power',
    ];

    protected $casts = [
        'channel' => 'integer',
        'current' => 'float',
        'current_raw' => 'integer',
        'power' => 'float',
    ];

    public function deviceReading(): BelongsTo
    {
        return $this->belongsTo(DeviceReading::class);
    }
}
