<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkforcesTable extends Migration
{
    public function up()
    {
        Schema::create('workforces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->foreignId('supply_center_id')->constrained('locations');
            $table->string('shift_times')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('workforces');
    }
}