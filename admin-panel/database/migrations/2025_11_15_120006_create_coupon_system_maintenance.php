<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates database triggers and stored procedures
     * for automated maintenance and cleanup tasks.
     */
    public function up(): void
    {
        // Create a cleanup job for expired active cart coupons
        DB::statement("
            CREATE EVENT IF NOT EXISTS cleanup_expired_cart_coupons
            ON SCHEDULE EVERY 1 HOUR
            DO
            DELETE FROM active_cart_coupons
            WHERE expires_at IS NOT NULL
            AND expires_at < NOW() - INTERVAL 1 DAY;
        ");

        // Create index for fast cleanup
        Schema::table('active_cart_coupons', function (Blueprint $table) {
            $table->index(['expires_at', 'created_at'], 'cleanup_idx');
        });

        // Create view for analytics dashboard
        DB::statement("
            CREATE VIEW coupon_analytics_summary AS
            SELECT
                c.id,
                c.code,
                c.nom,
                c.type,
                c.customer_type,
                COUNT(cul.id) as total_uses,
                COALESCE(SUM(cul.discount_amount), 0) as total_discount_given,
                COALESCE(AVG(cul.discount_amount), 0) as avg_discount_amount,
                COALESCE(SUM(cul.original_price - cul.final_price), 0) as total_savings,
                COUNT(DISTINCT cul.student_id) as unique_users,
                MAX(cul.used_at) as last_used_at,
                COALESCE(c.usage_count, 0) as current_usage_count,
                c.usage_limit,
                CASE
                    WHEN c.usage_limit IS NULL THEN 'unlimited'
                    WHEN c.usage_count >= c.usage_limit THEN 'exhausted'
                    WHEN c.usage_count / c.usage_limit > 0.8 THEN 'almost_full'
                    ELSE 'available'
                END as usage_status
            FROM coupons c
            LEFT JOIN coupon_usage_logs cul ON c.id = cul.coupon_id
            GROUP BY c.id, c.code, c.nom, c.type, c.customer_type, c.usage_count, c.usage_limit;
        ");

        // Create view for partner performance
        DB::statement("
            CREATE VIEW partner_performance_summary AS
            SELECT
                ap.id,
                ap.name,
                ap.email,
                ap.commission_rate,
                COUNT(DISTINCT c.id) as total_coupons,
                COUNT(pc.id) as total_commissions,
                COALESCE(SUM(pc.commission_amount), 0) as total_earned,
                COALESCE(SUM(CASE WHEN pc.status = 'paid' THEN pc.commission_amount ELSE 0 END), 0) as total_paid,
                COALESCE(SUM(CASE WHEN pc.status = 'pending' THEN pc.commission_amount ELSE 0 END), 0) as pending_amount,
                COUNT(DISTINCT cul.student_id) as unique_customers_referred,
                COALESCE(AVG(pc.commission_amount), 0) as avg_commission,
                MAX(pc.created_at) as last_commission_date
            FROM affiliate_partners ap
            LEFT JOIN coupons c ON ap.id = c.affiliate_partner_id
            LEFT JOIN coupon_usage_logs cul ON c.id = cul.coupon_id
            LEFT JOIN partner_commissions pc ON ap.id = pc.partner_id
            WHERE ap.is_active = 1
            GROUP BY ap.id, ap.name, ap.email, ap.commission_rate;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the event
        DB::statement("DROP EVENT IF EXISTS cleanup_expired_cart_coupons;");

        // Drop the views
        DB::statement("DROP VIEW IF EXISTS coupon_analytics_summary;");
        DB::statement("DROP VIEW IF EXISTS partner_performance_summary;");

        // Drop the cleanup index
        Schema::table('active_cart_coupons', function (Blueprint $table) {
            $table->dropIndex('cleanup_idx');
        });
    }
};
