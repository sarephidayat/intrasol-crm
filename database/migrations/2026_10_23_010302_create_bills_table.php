<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['receivable', 'payable']);
            $table->string('bill_number')->unique();
            $table->foreignId('party_id')->constrained('parties');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('description');
            $table->bigInteger('amount');
            $table->bigInteger('paid_amount')->default(0);
            $table->bigInteger('outstanding_amount')->default(0);
            $table->enum('status', ['draft', 'unpaid', 'partially_paid', 'paid', 'overdue', 'archived'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['type', 'status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
