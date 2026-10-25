<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('director_advances', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('person_name');
            $table->date('advance_date');
            $table->bigInteger('amount');
            $table->bigInteger('reimbursed_amount')->default(0);
            $table->bigInteger('outstanding_amount')->default(0);
            $table->string('purpose');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->enum('status', ['open', 'partially_reimbursed', 'reimbursed', 'closed'])->default('open');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['status', 'advance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('director_advances');
    }
};
