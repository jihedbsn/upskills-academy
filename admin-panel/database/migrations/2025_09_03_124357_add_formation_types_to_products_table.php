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
        if (!Schema::hasColumn('products', 'is_classroom')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('is_classroom')->default(false)->after('is_stage');
            });
        }
        if (!Schema::hasColumn('products', 'is_zoom')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('is_zoom')->default(false)->after('is_classroom');
            });
        }
        if (!Schema::hasColumn('products', 'is_online')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('is_online')->default(false)->after('is_zoom');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'is_online')) {
                $table->dropColumn('is_online');
            }
            if (Schema::hasColumn('products', 'is_zoom')) {
                $table->dropColumn('is_zoom');
            }
            if (Schema::hasColumn('products', 'is_classroom')) {
                $table->dropColumn('is_classroom');
            }
        });
    }
};
