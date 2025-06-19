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
        Schema::create('production_lines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('inactive'); // active, inactive, maintenance
            $table->float('capacity')->default(100); // Maximum capacity in units per hour
            $table->string('current_batch')->nullable();
            $table->float('efficiency')->default(100); // Current efficiency percentage
            $table->string('maintenance_status')->default('up_to_date'); // up_to_date, due_soon, overdue
            $table->timestamp('last_maintenance_date')->nullable();
            $table->timestamp('next_maintenance_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_lines');
    }
};
