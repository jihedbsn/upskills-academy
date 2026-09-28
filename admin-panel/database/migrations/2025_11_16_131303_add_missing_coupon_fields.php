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
            // Add missing fields
            $table->boolean('first_purchase_only')->default(false)->after('is_public');
            $table->boolean('cumulative_enabled')->default(false)->after('first_purchase_only');

            // Add indexes for performance
            $table->index(['first_purchase_only']);
            $table->index(['cumulative_enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['first_purchase_only']);
            $table->dropIndex(['cumulative_enabled']);

            // Drop columns
            $table->dropColumn(['first_purchase_only', 'cumulative_enabled']);
        });
    }
};
