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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Relasi ke User yang melakukan aksi
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Jenis Aksi: CREATE, UPDATE, DELETE
            $table->string('action', 50);

            // Nama Tabel (e.g., 'products', 'ingredients')
            $table->string('table_name', 100)->index();

            // ID dari record yang dimodifikasi
            $table->unsignedBigInteger('record_id')->index();

            // Data sebelum perubahan (NULL jika aksinya CREATE)
            $table->json('old_data')->nullable();

            // Data sesudah perubahan (NULL jika aksinya DELETE)
            $table->json('new_data')->nullable();

            // Menggunakan useCurrent karena log audit bersifat immutable (tidak diedit)
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};