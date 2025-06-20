<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@coffee.co.ug',
            'password' => Hash::make('admin@123'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
    }
}
