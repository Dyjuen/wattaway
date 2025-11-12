<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Check if admin already exists
        $adminExists = Account::where('email', 'admin@wattaway.com')->exists();

        if (! $adminExists) {
            $account = Account::create([
                'username' => 'admin',
                'email' => 'admin@wattaway.com',
                'password' => Hash::make('Admin@123'), // Change in production!
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);

            $token = $account->createToken('admin-token')->plainTextToken;
            $account->forceFill(['api_token' => $token])->save();

            $this->command->info('✅ Admin user created successfully!');
            $this->command->info('📧 Email: admin@wattaway.com');
            $this->command->info('🔑 Password: Admin@123');
            $this->command->warn('⚠️  Please change the password immediately in production!');
        } else {
            $this->command->info('ℹ️  Admin user already exists.');
        }
    }
}
