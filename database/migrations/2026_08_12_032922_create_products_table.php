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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // category_name, name, description, price_per_piece, is_available, available_quantity
            $table->string('category_name');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price_per_piece', 10, 2);
            $table->boolean('is_available')->default(true);
            $table->integer('available_quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
