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
        // Vérifier si la table existe déjà
        if (!Schema::hasTable('historique_quizzes')) {
            Schema::create('historique_quizzes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->integer('score')->default(0);
                $table->boolean('success')->default(false);
                $table->integer('attempts')->default(1);
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->json('answers')->nullable();
                $table->integer('time_spent')->nullable()->comment('Time spent in seconds');
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                // Indexes
                $table->index(['student_id', 'quiz_id']);
                $table->index(['product_id']);
                $table->index('completed_at');
            });
        } else {
            // Si la table existe, vérifier et ajouter les colonnes manquantes
            Schema::table('historique_quizzes', function (Blueprint $table) {
                if (!Schema::hasColumn('historique_quizzes', 'score')) {
                    $table->integer('score')->default(0)->after('product_id');
                }
                if (!Schema::hasColumn('historique_quizzes', 'success')) {
                    $table->boolean('success')->default(false)->after('score');
                }
                if (!Schema::hasColumn('historique_quizzes', 'attempts')) {
                    $table->integer('attempts')->default(1)->after('success');
                }
                if (!Schema::hasColumn('historique_quizzes', 'started_at')) {
                    $table->timestamp('started_at')->nullable()->after('attempts');
                }
                if (!Schema::hasColumn('historique_quizzes', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()->after('started_at');
                }
                if (!Schema::hasColumn('historique_quizzes', 'answers')) {
                    $table->json('answers')->nullable()->after('completed_at');
                }
                if (!Schema::hasColumn('historique_quizzes', 'time_spent')) {
                    $table->integer('time_spent')->nullable()->comment('Time spent in seconds')->after('answers');
                }
                if (!Schema::hasColumn('historique_quizzes', 'ip_address')) {
                    $table->string('ip_address')->nullable()->after('time_spent');
                }
                if (!Schema::hasColumn('historique_quizzes', 'user_agent')) {
                    $table->text('user_agent')->nullable()->after('ip_address');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_quizzes');
    }
};

