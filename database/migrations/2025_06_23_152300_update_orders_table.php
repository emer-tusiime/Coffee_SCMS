<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this import for DB facade

return new class extends Migration
{
    public function up()
    {
        // 1. Change status to string and allow nulls for migration
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->nullable()->change();
        });

        // 2. Migrate existing boolean/integer/null statuses to string values
        DB::table('orders')->where('status', false)->update(['status' => 'pending']);
        DB::table('orders')->where('status', true)->update(['status' => 'approved']);
        DB::table('orders')->whereNull('status')->update(['status' => 'pending']); // Default to 'pending' if null

        // 3. Make status not nullable again, with default 'pending'
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('pending')->nullable(false)->change();
        });

        // 4. Ensure 'wholesaler' is included in the ENUM for order_type
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_type')->default('retailer');
            $table->foreignId('supplier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('factory_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('delivery_date')->nullable();
            $table->boolean('payment_status')->default(false);
            $table->string('payment_method')->nullable();
            $table->text('shipping_address')->nullable();
            $table->timestamp('estimated_delivery_date')->nullable();
            $table->integer('priority_level')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'order_type',
                'supplier_id',
                'factory_id',
                'delivery_date',
                'payment_status',
                'payment_method',
                'shipping_address',
                'estimated_delivery_date',
                'priority_level',
                'created_by',
                'approved_by',
                'approved_at',
                'shipped_at',
                'delivered_at'
            ]);
        });
    }
};
