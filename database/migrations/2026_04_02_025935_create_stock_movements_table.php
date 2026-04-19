<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('ingredient_id')
                ->constrained('ingredients')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Tipe: 'IN' (Restock/Pembelian) atau 'OUT' (Penjualan/Rusak)
            $table->string('type', 10); 
            
            $table->decimal('quantity', 10, 2);

            // Referensi bisa berisi Kode Transaksi atau ID Restock
            $table->string('reference', 50)->nullable()->index();
            
            $table->text('description')->nullable();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
