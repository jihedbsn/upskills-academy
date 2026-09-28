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
        if (!Schema::hasColumn('blogs', 'description_short_ar')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->text('description_short_ar')->nullable()->after('description_en');
            });
        }
        if (!Schema::hasColumn('blogs', 'description_short_en')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->text('description_short_en')->nullable()->after('description_short_ar');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            if (Schema::hasColumn('blogs', 'description_short_en')) {
                $table->dropColumn('description_short_en');
            }
            if (Schema::hasColumn('blogs', 'description_short_ar')) {
                $table->dropColumn('description_short_ar');
            }
        });
    }
};
