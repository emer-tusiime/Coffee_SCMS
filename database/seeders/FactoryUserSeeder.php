<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FactoryUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Factory Manager',
            'email' => 'factory@scms.com',
            'password' => Hash::make('factory123'),
            'role' => 'factory',
            'email_verified_at' => now(),
        ]);
    }
}
