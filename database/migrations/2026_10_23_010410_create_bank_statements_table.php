<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_statements', function (Blueprint $table) {
            $table->id();
            $table->string('account_name')->default('Bank Jago');
            $table->date('period_start');
            $table->date('period_end');
            $table->bigInteger('opening_balance')->default(0);
            $table->bigInteger('closing_balance')->default(0);
            $table->enum('status', ['draft', 'in_progress', 'completed', 'reopened'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_statements');
    }
};
