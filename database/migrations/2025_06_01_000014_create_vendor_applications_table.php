<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('vendor_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->string('pdf_path');
            $table->string('status')->default('pending');
            $table->dateTime('submission_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_applications');
    }
}