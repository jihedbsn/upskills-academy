<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration adds the foreign key constraint for affiliate_partner_id
     * in the coupons table and additional performance optimizations.
     */
    public function up(): void
    {
        // Add foreign key constraint for affiliate partners
        Schema::table('coupons', function (Blueprint $table) {
            $table->foreign('affiliate_partner_id')->references('id')->on('affiliate_partners')->onDelete('set null');
        });

        // Add additional indexes for better query performance
        Schema::table('coupons', function (Blueprint $table) {
            // Composite indexes for common query patterns
            $table->index(['is_active', 'date_debut', 'date_fin'], 'active_dates_idx');
            $table->index(['customer_type', 'is_active'], 'customer_active_idx');
            $table->index(['auto_apply', 'is_active'], 'auto_apply_active_idx');
            $table->index(['type', 'is_active', 'customer_type'], 'type_active_customer_idx');
        });

        // Add indexes to coupon_detailles for better join performance
        Schema::table('coupon_detailles', function (Blueprint $table) {
            $table->index(['product_id', 'coupon_id'], 'product_coupon_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            // Drop composite indexes
            $table->dropIndex('active_dates_idx');
            $table->dropIndex('customer_active_idx');
            $table->dropIndex('auto_apply_active_idx');
            $table->dropIndex('type_active_customer_idx');

            // Drop foreign key constraint
            $table->dropForeign(['affiliate_partner_id']);
        });

        Schema::table('coupon_detailles', function (Blueprint $table) {
            $table->dropIndex('product_coupon_idx');
        });
    }
};
