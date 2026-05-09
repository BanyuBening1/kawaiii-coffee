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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->index();
            $table->decimal('stock', 10, 2);
            $table->string('unit', 20);
            $table->decimal('min_stock', 10, 2)->default(0);
            $table->index(
                    ['stock', 'min_stock'],
                    'idx_ing_stock'
                );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
