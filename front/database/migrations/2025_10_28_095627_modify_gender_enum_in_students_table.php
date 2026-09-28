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
        // Modifier l'enum pour retirer 'other'
        DB::statement("ALTER TABLE students MODIFY COLUMN gender ENUM('male', 'female') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remettre l'enum avec 'other'
        DB::statement("ALTER TABLE students MODIFY COLUMN gender ENUM('male', 'female', 'other') NULL");
    }
};
