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
        Schema::create('retailer_wholesaler', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('retailer_id');
            $table->unsignedBigInteger('wholesaler_id');
            $table->timestamps();

            $table->foreign('retailer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('wholesaler_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['retailer_id', 'wholesaler_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retailer_wholesaler');
    }
}; 