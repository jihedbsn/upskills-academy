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
        Schema::table('coupons', function (Blueprint $table) {
            // Allow multiple uses but not in same order
            $table->boolean('allow_multiple_uses')->default(false)->after('cumulative_enabled');

            // Add index for performance
            $table->index(['allow_multiple_uses']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            // Drop index
            $table->dropIndex(['allow_multiple_uses']);

            // Drop column
            $table->dropColumn('allow_multiple_uses');
        });
    }
};
