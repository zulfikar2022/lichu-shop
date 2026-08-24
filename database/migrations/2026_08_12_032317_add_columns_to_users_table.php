<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // is_superadmin = false(default), is_admin=false(default), is_customer=true (default), is_active=true (default)
            $table->boolean('is_superadmin')->default(false);
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_customer')->default(true);
            $table->boolean('is_active')->default(true);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
