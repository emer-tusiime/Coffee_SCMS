<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'approved')) {
                $table->boolean('approved')->default(false)->after('status');
            }
            if (!Schema::hasColumn('users', 'approval_message')) {
                $table->text('approval_message')->nullable()->after('approved');
            }
            if (!Schema::hasColumn('users', 'contact_info')) {
                $table->string('contact_info')->nullable()->after('password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'approved')) {
                $table->dropColumn('approved');
            }
            if (Schema::hasColumn('users', 'approval_message')) {
                $table->dropColumn('approval_message');
            }
            if (Schema::hasColumn('users', 'contact_info')) {
                $table->dropColumn('contact_info');
            }
        });
    }
};
