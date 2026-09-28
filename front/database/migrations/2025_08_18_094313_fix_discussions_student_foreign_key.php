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
        Schema::table('discussions', function (Blueprint $table) {
            // Supprimer la contrainte existante qui pointe vers users
            $table->dropForeign(['student_id']);
            
            // Recréer la contrainte pour pointer vers students
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discussions', function (Blueprint $table) {
            // Supprimer la contrainte vers students
            $table->dropForeign(['student_id']);
            
            // Recréer la contrainte vers users (rollback)
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
