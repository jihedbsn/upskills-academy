<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('practical_exam_attempts', function (Blueprint $table) {
            // Drop existing FK to students
            $table->dropForeign(['reviewed_by']);
        });

        // Clear existing reviewer values to avoid FK conflicts
        DB::table('practical_exam_attempts')->update(['reviewed_by' => null]);

        Schema::table('practical_exam_attempts', function (Blueprint $table) {
            // Point reviewer to admins table
            $table->foreign('reviewed_by')->references('id')->on('admins')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practical_exam_attempts', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
        });

        // Clear reviewer values before restoring original FK
        DB::table('practical_exam_attempts')->update(['reviewed_by' => null]);

        Schema::table('practical_exam_attempts', function (Blueprint $table) {
            $table->foreign('reviewed_by')->references('id')->on('students')->nullOnDelete();
        });
    }
};
