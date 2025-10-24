<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirmwareVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'description',
        'file_path',
        'file_size',
        'checksum',
        'is_stable',
        'required_version',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'is_stable' => 'boolean',
            'released_at' => 'datetime',
        ];
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function scopeStable($query)
    {
        return $query->where('is_stable', true);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('version', 'desc');
    }

    public function isNewerThan(string $version): bool
    {
        return version_compare($this->version, $version, '>');
    }
}
