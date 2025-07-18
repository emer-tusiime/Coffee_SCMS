<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('factories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('location');
            $table->integer('capacity');
            $table->string('processing_type');
            $table->string('quality_standard');
            $table->boolean('status')->default(true);
            $table->string('contact_person');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->json('operating_hours')->nullable();
            $table->json('certifications')->nullable();
            $table->timestamps();
        });

        Schema::create('supplier_factory', function (Blueprint $table) {
            $table->foreignId('factory_id')->constrained('factories')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['factory_id', 'supplier_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_factory');
        Schema::dropIfExists('factories');
    }
};
