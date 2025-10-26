<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FirmwareVersion>
 */
class FirmwareVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'version' => $this->faker->semver(),
            'description' => $this->faker->sentence(),
            'file_path' => 'firmware/test.bin',
            'file_size' => 12345,
            'checksum' => md5('test'),
            'is_stable' => true,
            'released_at' => now(),
        ];
    }
}
