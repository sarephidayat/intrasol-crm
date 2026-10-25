<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_statement_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_statement_id')->constrained('bank_statements')->cascadeOnDelete();
            $table->date('entry_date');
            $table->string('description');
            $table->string('reference')->nullable();
            $table->bigInteger('amount');
            $table->enum('type', ['debit', 'credit']);
            $table->enum('status', ['unmatched', 'matched', 'discrepancy'])->default('unmatched');
            $table->foreignId('cash_transaction_id')->nullable()->constrained('cash_transactions')->nullOnDelete();
            $table->foreignId('matched_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('matched_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['bank_statement_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_statement_entries');
    }
};
