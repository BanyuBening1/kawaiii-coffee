<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_ingredients', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Produk
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Relasi ke Bahan Baku
            $table->foreignId('ingredient_id')
                ->constrained('ingredients')
                ->cascadeOnDelete();

            // Jumlah kebutuhan bahan untuk 1 porsi/produk
            $table->decimal('quantity', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_ingredients');
    }
};
