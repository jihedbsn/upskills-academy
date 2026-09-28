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
        Schema::table('student_stage_courses', function (Blueprint $table) {
            // Modifier le champ is_valid pour accepter -1, 0, 1
            $table->tinyInteger('is_valid')->default(0)->change()->comment('0: En attente, 1: Validé, -1: Non validé');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_stage_courses', function (Blueprint $table) {
            // Revenir au boolean
            $table->boolean('is_valid')->default(false)->change();
        });
    }
};