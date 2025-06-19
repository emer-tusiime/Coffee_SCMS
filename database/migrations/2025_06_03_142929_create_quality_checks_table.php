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
        Schema::create('quality_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_line_id')->constrained()->onDelete('cascade');
            $table->string('batch_number');
            $table->float('temperature')->comment('Temperature during roasting (°C)');
            $table->float('humidity')->comment('Humidity percentage');
            $table->enum('roast_level', ['light', 'medium', 'medium-dark', 'dark'])->default('medium');
            $table->float('aroma_score')->comment('Score from 0-10');
            $table->float('flavor_score')->comment('Score from 0-10');
            $table->float('acidity_score')->comment('Score from 0-10');
            $table->float('body_score')->comment('Score from 0-10');
            $table->float('aftertaste_score')->comment('Score from 0-10');
            $table->float('overall_score')->comment('Score from 0-10');
            $table->float('moisture_content')->comment('Percentage');
            $table->integer('defect_count')->default(0);
            $table->foreignId('checked_by')->constrained('users');
            $table->enum('status', ['pending', 'passed', 'failed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Add indexes for frequently queried columns
            $table->index('batch_number');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_checks');
    }
};
