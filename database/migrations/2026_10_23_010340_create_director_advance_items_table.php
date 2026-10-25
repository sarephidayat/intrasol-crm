<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('director_advance_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('director_advance_id')->constrained('director_advances')->cascadeOnDelete();
            $table->date('expense_date');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('description');
            $table->bigInteger('amount');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('receipt_path')->nullable();
            $table->enum('status', ['submitted', 'approved', 'rejected'])->default('submitted');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('director_advance_items');
    }
};
