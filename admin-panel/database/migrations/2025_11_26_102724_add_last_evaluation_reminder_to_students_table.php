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
        // Fix the gender column ENUM issue first, then add our column
        DB::statement("ALTER TABLE students MODIFY gender ENUM('male', 'female') NULL");

        Schema::table('students', function (Blueprint $table) {
            $table->timestamp('last_evaluation_reminder')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('last_evaluation_reminder');
        });
    }
};
