<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CheckAdminUser extends Command
{
    protected $signature = 'check:admin-user';
    protected $description = 'Check admin user details';

    public function handle()
    {
        $adminEmail = 'admin@coffee.co.ug';
        $adminPassword = 'admin@123';

        $user = User::where('email', $adminEmail)->first();

        if (!$user) {
            $this->error("Admin user not found in database!");
            $this->info("Run 'php artisan db:seed --class=AdminSeeder' to create the admin user");
            return;
        }

        $this->info("Admin User Details:");
        $this->info("------------------");
        $this->info("Email: {$user->email}");
        $this->info("Role: {$user->role}");
        $this->info("Status: {$user->status}");
        $this->info("Approved: " . ($user->approved ? 'Yes' : 'No'));
        $this->info("Password matches: " . (Hash::check($adminPassword, $user->password) ? 'Yes' : 'No'));

        if ($user->hasRole('admin') && $user->isActive()) {
            $this->info("\n✅ Admin user is properly configured and can login");
        } else {
            $this->error("\n❌ Admin user has issues:");
            if (!$user->hasRole('admin')) {
                $this->error("- User is not an admin");
            }
            if (!$user->isActive()) {
                $this->error("- User is not active");
            }
        }

        $factoriesAdded = 0;
        $factoryUsers = User::where('role', 'factory')->get();
        foreach ($factoryUsers as $user) {
            if (!$user->factory) {
                Factory::create([
                    'user_id' => $user->id,
                    'name' => $user->name . "'s Factory",
                    'location' => $user->contact_info ?? 'Unknown',
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
                $factoriesAdded++;
            }
        }
        $this->info("Added $factoriesAdded missing factory records.");
        return 0;
    }
}
