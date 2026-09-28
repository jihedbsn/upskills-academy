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
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'titre_en')) {
                $table->string('titre_en')->nullable();
            }
            if (!Schema::hasColumn('categories', 'titre_ar')) {
                $table->string('titre_ar')->nullable();
            }
            if (!Schema::hasColumn('categories', 'titre')) {
                $table->string('titre')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['titre_en', 'titre_ar', 'titre']);
        });
    }
};
