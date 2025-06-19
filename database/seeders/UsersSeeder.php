<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@scms.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Supplier User',
                'email' => 'supplier@scms.com',
                'password' => Hash::make('password'),
                'role' => 'supplier',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Factory Manager',
                'email' => 'factory@scms.com',
                'password' => Hash::make('password'),
                'role' => 'factory',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Retail Store Rep',
                'email' => 'retail@scms.com',
                'password' => Hash::make('password'),
                'role' => 'retailer',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Customer Service',
                'email' => 'customer@scms.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
