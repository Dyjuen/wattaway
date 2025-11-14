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

    // The scopeLatest method was removed because it used an incorrect lexicographical sort.
    // Correct semantic version sorting is now handled in the OtaController.

    public function isNewerThan(string $version): bool
    {
        return version_compare($this->version, $version, '>');
    }
}
