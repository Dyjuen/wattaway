<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test account (or get existing one)
        $account = \App\Models\Account::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'username' => 'testuser',
                'password' => bcrypt('password'),
            ]
        );

        $this->call([
            ProductSeeder::class,
            DeviceSeeder::class,
        ]);
    }
}
