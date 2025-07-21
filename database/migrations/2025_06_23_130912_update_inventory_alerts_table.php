<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('retailer_inventory_alerts', function (Blueprint $table) {
            // Rename item_id to product_id
            if (Schema::hasColumn('retailer_inventory_alerts', 'item_id') && !Schema::hasColumn('retailer_inventory_alerts', 'product_id')) {
                $table->renameColumn('item_id', 'product_id');
            }

            // Add new columns if they do not exist
            if (!Schema::hasColumn('retailer_inventory_alerts', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('retailer_inventory_alerts', 'last_checked_at')) {
                $table->timestamp('last_checked_at')->nullable();
            }
            if (!Schema::hasColumn('retailer_inventory_alerts', 'alert_frequency')) {
                $table->integer('alert_frequency')->default(1); // in days
            }
            if (!Schema::hasColumn('retailer_inventory_alerts', 'alert_method')) {
                $table->string('alert_method')->default('email'); // email, sms, both
            }
            if (!Schema::hasColumn('retailer_inventory_alerts', 'notes')) {
                $table->text('notes')->nullable();
            }

            // Update existing columns
            if (Schema::hasColumn('retailer_inventory_alerts', 'alert_type')) {
                $table->string('alert_type')->change();
            }
            if (Schema::hasColumn('retailer_inventory_alerts', 'message')) {
                $table->text('message')->change();
            }
            if (Schema::hasColumn('retailer_inventory_alerts', 'status')) {
                $table->boolean('status')->change();
            }
        });

        // Add foreign key constraint if not exists
        if (!DB::select("SELECT * FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'retailer_inventory_alerts' AND COLUMN_NAME = 'product_id' AND REFERENCED_TABLE_NAME = 'products'")) {
            Schema::table('retailer_inventory_alerts', function (Blueprint $table) {
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::table('retailer_inventory_alerts', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['product_id']);

            // Drop new columns
            $table->dropColumn(['is_active', 'last_checked_at', 'alert_frequency', 'alert_method', 'notes']);

            // Rename product_id back to item_id
            $table->renameColumn('product_id', 'item_id');

            // Update existing columns back to original types
            $table->string('alert_type')->nullable()->change();
            $table->text('message')->nullable()->change();
            $table->boolean('status')->default(false)->change();
        });
    }
};
