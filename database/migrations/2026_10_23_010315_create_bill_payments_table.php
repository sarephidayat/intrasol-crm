<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bill_payments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('bill_id')->constrained('bills');
            $table->foreignId('cash_transaction_id')->constrained('cash_transactions');
            $table->date('payment_date');
            $table->bigInteger('amount');
            $table->string('payment_method')->default('bank_transfer');
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['bill_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_payments');
    }
};
