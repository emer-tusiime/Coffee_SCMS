<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FactoryUserSeeder extends Seeder
{
    public function run()
    {
        $user = User::create([
            'name' => 'Factory Manager',
            'email' => 'factory@scms.com',
            'password' => Hash::make('factory123'),
            'role' => 'factory',
            'email_verified_at' => now(),
        ]);

        // Create a Factory record for this user
        \App\Models\Factory::create([
            'user_id' => $user->id,
            'name' => 'Default Factory',
            'location' => 'Default Location',
            'capacity' => 1000,
            'processing_type' => 'Washed',
            'quality_standard' => 'A',
            'status' => true,
            'contact_person' => $user->name,
            'contact_email' => $user->email,
            'contact_phone' => '0000000000',
            'operating_hours' => json_encode(['mon-fri' => '8-5']),
            'certifications' => json_encode(['ISO9001']),
        ]);
    }
}
