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
        Schema::table('quiz_products', function (Blueprint $table) {
            $table->boolean('use_own_questions')->default(false)->after('score_success')->comment('If true, fetch questions from exam itself, otherwise from course quizzes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_products', function (Blueprint $table) {
            $table->dropColumn('use_own_questions');
        });
    }
};
