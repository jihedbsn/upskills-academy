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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('has_theoretical_exam')->default(false)->after('max_exam_attempts');
            $table->boolean('has_practical_exam')->default(false)->after('has_theoretical_exam');
            $table->enum('practical_exam_type', ['online', 'classroom'])->nullable()->after('has_practical_exam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['has_theoretical_exam', 'has_practical_exam', 'practical_exam_type']);
        });
    }
};
