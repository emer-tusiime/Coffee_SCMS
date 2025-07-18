<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Get the first supplier user
        $supplier = DB::table('users')->where('role', 'supplier')->first();
        if ($supplier) {
            DB::table('orders')->whereNull('supplier_id')->update(['supplier_id' => $supplier->id]);
        }
    }

    public function down()
    {
        // Optionally set supplier_id back to null (not recommended)
        // DB::table('orders')->whereNotNull('supplier_id')->update(['supplier_id' => null]);
    }
}; 