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
            // Supprimer les anciennes contraintes de clé étrangère
            $table->dropForeign(['question_id']);
            $table->dropForeign(['response_id']);
        });

        Schema::table('historique_quizzes', function (Blueprint $table) {
            // Ajouter les nouvelles contraintes qui permettent NULL
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->foreign('response_id')->references('id')->on('reponse_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historique_quizzes', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
            $table->dropForeign(['response_id']);
        });

        Schema::table('historique_quizzes', function (Blueprint $table) {
            // Remettre les anciennes contraintes (qui ne permettaient pas NULL)
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->foreign('response_id')->references('id')->on('reponse_questions')->onDelete('cascade');
        });
    }
};
