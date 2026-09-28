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
        Schema::table('course_ratings', function (Blueprint $table) {
            // Supprimer la contrainte existante qui référence users
            $table->dropForeign(['student_id']);
            
            // Ajouter la nouvelle contrainte qui référence students
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_ratings', function (Blueprint $table) {
            // Supprimer la contrainte qui référence students
            $table->dropForeign(['student_id']);
            
            // Restaurer la contrainte qui référence users
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
