<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        if (!User::where('email', 'admin@coffee.co.ug')->exists()) {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@coffee.co.ug',
            'password' => Hash::make('admin@123'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        }
    }
}
