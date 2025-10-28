<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'name' => fake()->words(2, true) . ' Socket',
            'description' => fake()->sentence(),
            'status' => 'offline',
            'serial_number' => 'WS' . fake()->unique()->bothify('############'),
            'hardware_id' => 'ESP32-' . fake()->unique()->bothify('############'),
            'api_token' => Str::random(64),
        ];
    }
}
