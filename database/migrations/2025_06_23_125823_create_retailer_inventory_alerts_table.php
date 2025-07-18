<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('retailer_inventory_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retailer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('threshold')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_checked_at')->nullable();
            $table->integer('alert_frequency')->default(1); // in days
            $table->string('alert_method')->default('email'); // email, sms, both
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['retailer_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('retailer_inventory_alerts');
    }
};
