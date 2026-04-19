<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->string('transaction_code', 30)->unique();
            $table->dateTime('transaction_date');

            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);

            $table->decimal('paid_amount', 12, 2);
            $table->decimal('change_amount', 12, 2);

            // ENUM payment method
            $table->enum('payment_method', ['cash', 'qris', 'transfer']);

            // status transaksi
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('paid');

            // relasi ke users (cashier)
            $table->foreignId('cashier_id')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};