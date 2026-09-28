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
        Schema::table('practical_exam_attempts', function (Blueprint $table) {
            $table->boolean('case_downloaded')->default(false)->after('status');
            $table->timestamp('case_downloaded_at')->nullable()->after('case_downloaded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practical_exam_attempts', function (Blueprint $table) {
            $table->dropColumn(['case_downloaded', 'case_downloaded_at']);
        });
    }
};
