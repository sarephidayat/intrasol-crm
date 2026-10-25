<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('director_reimbursements', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('director_advance_id')->constrained('director_advances');
            $table->foreignId('cash_transaction_id')->constrained('cash_transactions');
            $table->date('payment_date');
            $table->bigInteger('amount');
            $table->string('payment_method')->default('bank_transfer');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('director_reimbursements');
    }
};
