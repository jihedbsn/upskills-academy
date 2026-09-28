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
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('adresse')->nullable()->after('phone');
            $table->string('facebook')->nullable()->after('adresse');
            $table->string('youtube')->nullable()->after('facebook');
            $table->string('instagram')->nullable()->after('youtube');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['phone', 'adresse', 'facebook', 'youtube', 'instagram']);
        });
    }
};

