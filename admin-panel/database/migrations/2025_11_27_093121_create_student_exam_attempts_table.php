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
        Schema::create('student_exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade')->comment('Quiz with type_id = 1 (exam)');
            $table->integer('attempts')->default(0)->comment('Number of exam attempts (only for quizzes with type_id = 1)');
            $table->boolean('is_blocked')->default(false)->comment('Whether student is blocked from taking this exam');
            $table->timestamp('blocked_at')->nullable()->comment('When the student was blocked from exam');
            $table->timestamp('renewal_email_sent_at')->nullable()->comment('When renewal email was sent');
            $table->timestamps();

            // Unique constraint: one record per student per product per exam
            $table->unique(['student_id', 'product_id', 'quiz_id']);

            // Indexes for performance
            $table->index(['student_id', 'is_blocked']);
            $table->index(['product_id', 'quiz_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_exam_attempts');
    }
};
