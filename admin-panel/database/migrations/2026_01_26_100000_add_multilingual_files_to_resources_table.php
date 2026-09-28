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
        Schema::table('resources', function (Blueprint $table) {
            // Add Arabic and English file columns
            if (!Schema::hasColumn('resources', 'file_ar')) {
                $table->string('file_ar')->nullable()->after('file');
            }
            if (!Schema::hasColumn('resources', 'file_en')) {
                $table->string('file_en')->nullable()->after('file_ar');
            }
        });

        // Migrate existing data: copy 'file' to both 'file_ar' and 'file_en' if they exist
        DB::table('resources')->whereNotNull('file')->update([
            'file_ar' => DB::raw('file'),
            'file_en' => DB::raw('file'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            if (Schema::hasColumn('resources', 'file_ar')) {
                $table->dropColumn('file_ar');
            }
            if (Schema::hasColumn('resources', 'file_en')) {
                $table->dropColumn('file_en');
            }
        });
    }
};
