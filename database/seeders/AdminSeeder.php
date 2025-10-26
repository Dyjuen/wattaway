<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Check if admin already exists
        $adminExists = Account::where('email', 'admin@wattaway.com')->exists();

        if (!$adminExists) {
            Account::create([
                'username' => 'admin',
                'email' => 'admin@wattaway.com',
                'password' => Hash::make('Admin@123'), // Change in production!
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);

            $this->command->info('✅ Admin user created successfully!');
            $this->command->info('📧 Email: admin@wattaway.com');
            $this->command->info('🔑 Password: Admin@123');
            $this->command->warn('⚠️  Please change the password immediately in production!');
        } else {
            $this->command->info('ℹ️  Admin user already exists.');
        }
    }
}
