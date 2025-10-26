<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create {email} {password} {name} {username}';
    protected $description = 'Create a new admin user';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->argument('name');
        $username = $this->argument('username');

        $validator = Validator::make([
            'email' => $email,
            'password' => $password,
            'name' => $name,
            'username' => $username,
        ], [
            'email' => 'required|email|unique:accounts,email',
            'password' => 'required|min:8',
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:accounts,username',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        Account::create([
            'name' => $name,
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->info("✅ Admin user created successfully!");
        $this->info("📧 Email: {$email}");
        
        return 0;
    }
}
