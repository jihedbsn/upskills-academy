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
        // Supprimer l'ancienne contrainte de clé étrangère vers users
        Schema::table('historique_quizzes', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
        });

        // Ajouter la nouvelle contrainte de clé étrangère vers la table students
        Schema::table('historique_quizzes', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer la contrainte vers students
        Schema::table('historique_quizzes', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
        });

        // Remettre l'ancienne contrainte vers users
        Schema::table('historique_quizzes', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
