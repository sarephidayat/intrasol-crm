<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_statement_id')->nullable()->constrained('bank_statements')->nullOnDelete();
            $table->date('reconciliation_date');
            $table->bigInteger('system_balance');
            $table->bigInteger('actual_bank_balance');
            $table->bigInteger('difference');
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'completed', 'reopened'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reconciliations');
    }
};
