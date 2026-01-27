<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Boss Account
        User::create([
            'name' => 'Boss Admin',
            'email' => 'boss@pos.com',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
            'role' => 'boss',
            'is_active' => true,
        ]);

        // Create Employee Accounts
        User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir1@pos.com',
            'phone' => '081234567891',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Kasir 2',
            'email' => 'kasir2@pos.com',
            'phone' => '081234567892',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'is_active' => true,
        ]);

        $this->command->info('✅ Users created successfully!');
        $this->command->info('📧 Boss: boss@pos.com | Password: password');
        $this->command->info('📧 Kasir1: kasir1@pos.com | Password: password');
        $this->command->info('📧 Kasir2: kasir2@pos.com | Password: password');
    }
}