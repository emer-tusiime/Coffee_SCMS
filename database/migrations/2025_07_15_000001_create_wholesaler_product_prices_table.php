<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('wholesaler_product_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wholesaler_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('price', 12, 2);
            $table->timestamps();

            $table->unique(['wholesaler_id', 'product_id']);
            $table->foreign('wholesaler_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wholesaler_product_prices');
    }
}; 