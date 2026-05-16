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
    DB::statement("ALTER TABLE transactions MODIFY COLUMN payment_method ENUM('cash', 'qris', 'transfer', 'midtrans', 'credit_card', 'gopay', 'bank_transfer', 'echannel', 'bca_va', 'bni_va', 'bri_va', 'other') NOT NULL");
}

public function down(): void
{
    DB::statement("ALTER TABLE transactions MODIFY COLUMN payment_method ENUM('cash', 'qris', 'transfer', 'midtrans') NOT NULL");
}
};
