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
        Schema::create('quality_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_line_id')->constrained()->onDelete('cascade');
            $table->string('issue_type');
            $table->text('description');
            $table->string('severity'); // low, medium, high, critical
            $table->string('status')->default('open'); // open, in_progress, resolved
            $table->foreignId('reported_by')->constrained('users');
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_issues');
    }
};
