<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Esp32MessageLog extends Model
{
    protected $table = 'esp32messagelogs';

    protected $fillable = [
        'device_id',
        'content',
        'direction',
        'metadata',
        'ip_address',
        'endpoint',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function scopeIncoming($query)
    {
        return $query->where('direction', 'incoming');
    }

    public function scopeOutgoing($query)
    {
        return $query->where('direction', 'outgoing');
    }
}
