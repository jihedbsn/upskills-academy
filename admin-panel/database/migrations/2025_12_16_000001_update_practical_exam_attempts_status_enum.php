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
        // First, modify the enum column to include new values
        DB::statement("ALTER TABLE practical_exam_attempts MODIFY COLUMN status ENUM('pending', 'pending_submission', 'pending_review', 'passed', 'failed') DEFAULT 'pending'");

        // Update existing 'pending' to 'pending_submission'
        DB::table('practical_exam_attempts')
            ->where('status', 'pending')
            ->whereNull('submitted_at')
            ->update(['status' => 'pending_submission']);

        // Update existing 'pending' with submission to 'pending_review'
        DB::table('practical_exam_attempts')
            ->where('status', 'pending')
            ->whereNotNull('submitted_at')
            ->update(['status' => 'pending_review']);

        // Finally, remove 'pending' from enum
        DB::statement("ALTER TABLE practical_exam_attempts MODIFY COLUMN status ENUM('pending_submission', 'pending_review', 'passed', 'failed') DEFAULT 'pending_submission'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to old enum
        DB::statement("ALTER TABLE practical_exam_attempts MODIFY COLUMN status ENUM('pending', 'passed', 'failed') DEFAULT 'pending'");

        // Revert status values
        DB::table('practical_exam_attempts')
            ->whereIn('status', ['pending_submission', 'pending_review'])
            ->update(['status' => 'pending']);
    }
};
