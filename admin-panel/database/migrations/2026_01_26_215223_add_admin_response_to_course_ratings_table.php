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
            $table->text('admin_response')->nullable()->after('commentaire');
            $table->timestamp('admin_response_at')->nullable()->after('admin_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_ratings', function (Blueprint $table) {
            $table->dropColumn(['admin_response', 'admin_response_at']);
        });
    }
};
