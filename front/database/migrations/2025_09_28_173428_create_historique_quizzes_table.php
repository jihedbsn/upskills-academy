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
        Schema::create('historique_quizzes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('quiz_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('response_id');
            $table->boolean('is_correct')->default(false);
            $table->tinyInteger('quiz_type'); // 1 = examen, 2 = quiz
            $table->integer('attempt_number')->default(1);
            $table->integer('total_questions')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->decimal('score_percentage', 5, 2)->default(0);
            $table->boolean('success')->default(false);
            $table->integer('time_spent')->nullable(); // en secondes
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Clés étrangères
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->foreign('response_id')->references('id')->on('reponse_questions')->onDelete('cascade');

            // Index pour les performances
            $table->index(['student_id', 'product_id']);
            $table->index(['student_id', 'quiz_id']);
            $table->index(['product_id', 'quiz_type']);
            $table->index(['student_id', 'quiz_type']);
            $table->index(['completed_at']);
            $table->index(['success']);
            
            // Index composite pour éviter les doublons de tentatives
            $table->unique(['student_id', 'quiz_id', 'attempt_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_quizzes');
    }
};
