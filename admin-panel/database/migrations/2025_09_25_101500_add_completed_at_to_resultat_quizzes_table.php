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
        if (Schema::hasTable('resultat_quizzes')) {
            Schema::table('resultat_quizzes', function (Blueprint $table) {
                if (!Schema::hasColumn('resultat_quizzes', 'started_at')) {
                    $table->timestamp('started_at')->nullable()->after('attempts');
                }
                if (!Schema::hasColumn('resultat_quizzes', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()->after('started_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('resultat_quizzes')) {
            Schema::table('resultat_quizzes', function (Blueprint $table) {
                if (Schema::hasColumn('resultat_quizzes', 'completed_at')) {
                    $table->dropColumn('completed_at');
                }
            });
        }
    }
};


