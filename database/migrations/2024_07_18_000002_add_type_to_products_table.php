<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Removed: type column is added as enum in a later migration
    }

    public function down()
    {
        // Removed: type column is dropped in a later migration
    }
}; 