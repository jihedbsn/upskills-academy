<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration adds a new column to store multilingual video files.
     * The new structure will be:
     * video_files_multilingual: [
     *     {
     *         title_ar: string,
     *         title_en: string,
     *         file_ar: string (filename),
     *         file_en: string (filename),
     *         uploaded_at: datetime
     *     },
     *     ...
     * ]
     */
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            if (!Schema::hasColumn('resources', 'video_files_multilingual')) {
                $table->json('video_files_multilingual')->nullable()->after('video_files');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            if (Schema::hasColumn('resources', 'video_files_multilingual')) {
                $table->dropColumn('video_files_multilingual');
            }
        });
    }
};
