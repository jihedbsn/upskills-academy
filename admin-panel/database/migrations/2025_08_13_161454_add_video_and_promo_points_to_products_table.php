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
        if (!Schema::hasColumn('products', 'video')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('video')->nullable()->after('point');
            });
        }
        if (!Schema::hasColumn('products', 'promo_points')) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('promo_points')->nullable()->after('video');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'video')) {
                $table->dropColumn('video');
            }
            if (Schema::hasColumn('products', 'promo_points')) {
                $table->dropColumn('promo_points');
            }
        });
    }
};
