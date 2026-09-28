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
        Schema::table('product_variations', function (Blueprint $table) {
            // Ensure columns expected by the app exist
            if (!Schema::hasColumn('product_variations', 'description_the_exams')) {
                $table->text('description_the_exams')->nullable()->after('short_description');
            }
            if (!Schema::hasColumn('product_variations', 'description_the_quizzes')) {
                $table->text('description_the_quizzes')->nullable()->after('description_the_exams');
            }
            if (!Schema::hasColumn('product_variations', 'description_final_exam')) {
                $table->text('description_final_exam')->nullable()->after('description_the_quizzes');
            }
            if (!Schema::hasColumn('product_variations', 'description_video_exam')) {
                $table->text('description_video_exam')->nullable()->after('description_final_exam');
            }
            if (!Schema::hasColumn('product_variations', 'description_stage')) {
                $table->text('description_stage')->nullable()->after('description_video_exam');
            }
            if (!Schema::hasColumn('product_variations', 'description_study_case')) {
                $table->text('description_study_case')->nullable()->after('description_stage');
            }

            // Add 'langue' used by the app, keep legacy 'lang' if present
            if (!Schema::hasColumn('product_variations', 'langue')) {
                $table->string('langue', 10)->nullable()->after('ad');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            if (Schema::hasColumn('product_variations', 'description_the_exams')) {
                $table->dropColumn('description_the_exams');
            }
            if (Schema::hasColumn('product_variations', 'description_the_quizzes')) {
                $table->dropColumn('description_the_quizzes');
            }
            if (Schema::hasColumn('product_variations', 'description_final_exam')) {
                $table->dropColumn('description_final_exam');
            }
            if (Schema::hasColumn('product_variations', 'description_video_exam')) {
                $table->dropColumn('description_video_exam');
            }
            if (Schema::hasColumn('product_variations', 'description_stage')) {
                $table->dropColumn('description_stage');
            }
            if (Schema::hasColumn('product_variations', 'description_study_case')) {
                $table->dropColumn('description_study_case');
            }
            if (Schema::hasColumn('product_variations', 'langue')) {
                $table->dropColumn('langue');
            }
        });
    }
};


