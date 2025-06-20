<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// AdminUserSeeder is now disabled. Use AdminSeeder for admin account creation.

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::create([
        //     'name' => 'Admin User',
        //     'email' => 'admin@coffee-scms.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'admin',
        //     'status' => 'active'
        // ]);
    }
}
