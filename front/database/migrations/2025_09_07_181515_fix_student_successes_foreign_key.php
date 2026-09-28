<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vérifier si la contrainte existe avant de la supprimer
        $foreignKeys = \DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'student_successes' 
            AND COLUMN_NAME = 'student_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        if (!empty($foreignKeys)) {
            Schema::table('student_successes', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
            });
        }
        
        Schema::table('student_successes', function (Blueprint $table) {
            // Add the new foreign key constraint referencing the 'students' table
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('student_successes', function (Blueprint $table) {
            // Revert to the old foreign key constraint if needed (e.g., for rollback)
            $table->dropForeign(['student_id']);
        });
        
        Schema::table('student_successes', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};