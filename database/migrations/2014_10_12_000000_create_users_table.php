<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role')->default('user');
            $table->string('password');
            $table->string('contact_info')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive', 'suspended'])->default('pending');
            $table->boolean('approved')->default(false);
            $table->text('approval_message')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Create initial admin user
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@coffee.co.ug',
            'password' => Hash::make('admin@123'),
            'role' => 'admin',
            'contact_info' => 'Admin Contact',
            'status' => 'active',
            'approved' => true,
            'approval_message' => 'Admin account',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
