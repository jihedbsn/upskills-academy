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
        // Modifier la table quizzes
        Schema::table('quizzes', function (Blueprint $table) {
            $table->text('name_ar')->change();
            $table->text('name_en')->change();
        });

        // Modifier la table questions
        Schema::table('questions', function (Blueprint $table) {
            $table->text('name_ar')->change();
            $table->text('name_en')->change();
        });

        // Modifier la table reponse_questions
        Schema::table('reponse_questions', function (Blueprint $table) {
            $table->text('titre_ar')->change();
            $table->text('titre_en')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir aux colonnes VARCHAR(255)
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('name_ar', 255)->change();
            $table->string('name_en', 255)->change();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->string('name_ar', 255)->change();
            $table->string('name_en', 255)->change();
        });

        Schema::table('reponse_questions', function (Blueprint $table) {
            $table->string('titre_ar', 255)->change();
            $table->string('titre_en', 255)->change();
        });
    }
};