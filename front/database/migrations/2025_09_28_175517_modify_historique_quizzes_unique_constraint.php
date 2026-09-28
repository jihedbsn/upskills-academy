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
        Schema::table('historique_quizzes', function (Blueprint $table) {
            // Supprimer l'ancienne contrainte d'unicité
            $table->dropUnique(['student_id', 'quiz_id', 'attempt_number']);
        });

        Schema::table('historique_quizzes', function (Blueprint $table) {
            // Ajouter une nouvelle contrainte d'unicité qui inclut question_id
            // Cela permet d'avoir un résumé (question_id = NULL) + des détails par question
            $table->unique(['student_id', 'quiz_id', 'attempt_number', 'question_id'], 'unique_attempt_question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historique_quizzes', function (Blueprint $table) {
            // Supprimer la nouvelle contrainte
            $table->dropUnique('unique_attempt_question');
        });

        Schema::table('historique_quizzes', function (Blueprint $table) {
            // Remettre l'ancienne contrainte
            $table->unique(['student_id', 'quiz_id', 'attempt_number']);
        });
    }
};
