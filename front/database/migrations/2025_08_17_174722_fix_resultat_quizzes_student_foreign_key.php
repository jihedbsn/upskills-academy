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
        Schema::table('resultat_quizzes', function (Blueprint $table) {
            // Supprimer l'ancienne contrainte
            $table->dropForeign(['student_id']);
            
            // Ajouter la nouvelle contrainte vers la table students
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resultat_quizzes', function (Blueprint $table) {
            // Supprimer la nouvelle contrainte
            $table->dropForeign(['student_id']);
            
            // Remettre l'ancienne contrainte
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
