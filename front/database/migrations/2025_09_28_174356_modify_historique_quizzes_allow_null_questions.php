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
            // Modifier question_id et response_id pour permettre NULL
            $table->unsignedBigInteger('question_id')->nullable()->change();
            $table->unsignedBigInteger('response_id')->nullable()->change();
            $table->boolean('is_correct')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historique_quizzes', function (Blueprint $table) {
            // Remettre les champs comme non-nullables
            $table->unsignedBigInteger('question_id')->nullable(false)->change();
            $table->unsignedBigInteger('response_id')->nullable(false)->change();
            $table->boolean('is_correct')->default(false)->change();
        });
    }
};
