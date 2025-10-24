<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceConfiguration extends Model
{
    use HasFactory;

    protected $fillable = ['device_id', 'key', 'value'];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
