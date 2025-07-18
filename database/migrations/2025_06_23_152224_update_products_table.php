<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('factory_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['raw', 'processed'])->default('raw');
            $table->string('quality_grade')->nullable();
            $table->string('units')->default('kg');
            $table->integer('min_order_quantity')->default(1);
            $table->integer('max_order_quantity')->nullable();
            $table->integer('lead_time_days')->default(0);
            $table->timestamp('last_updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'factory_id',
                'type',
                'quality_grade',
                'units',
                'min_order_quantity',
                'max_order_quantity',
                'lead_time_days',
                'last_updated_at'
            ]);
        });
    }
};
