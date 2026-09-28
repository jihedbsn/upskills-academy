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
            // Stackable coupon system
            $table->boolean('is_stackable')->default(false)->after('is_active');
            $table->integer('stack_priority')->default(1)->after('is_stackable');

            // Customer type restrictions
            $table->enum('customer_type', ['all', 'new', 'returning', 'vip'])->default('all')->after('stack_priority');

            // Auto-apply system
            $table->boolean('auto_apply')->default(false)->after('customer_type');
            $table->json('auto_apply_conditions')->nullable()->after('auto_apply');

            // Enhanced restrictions
            $table->decimal('max_discount_amount', 10, 2)->nullable()->after('montant_minimum');
            $table->integer('min_items')->nullable()->after('max_discount_amount');
            $table->json('course_types')->nullable()->after('min_items');
            $table->integer('max_uses_per_user')->nullable()->after('course_types');

            // Affiliate/Partner system
            $table->unsignedBigInteger('affiliate_partner_id')->nullable()->after('max_uses_per_user');
            $table->decimal('commission_rate', 5, 2)->nullable()->after('affiliate_partner_id');

            // Public visibility
            $table->boolean('is_public')->default(false)->after('commission_rate');

            // Add indexes for performance
            $table->index(['is_stackable']);
            $table->index(['customer_type']);
            $table->index(['auto_apply']);
            $table->index(['is_public']);
            $table->index(['affiliate_partner_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex(['is_stackable']);
            $table->dropIndex(['customer_type']);
            $table->dropIndex(['auto_apply']);
            $table->dropIndex(['is_public']);
            $table->dropIndex(['affiliate_partner_id']);

            $table->dropColumn([
                'is_stackable',
                'stack_priority',
                'customer_type',
                'auto_apply',
                'auto_apply_conditions',
                'max_discount_amount',
                'min_items',
                'course_types',
                'max_uses_per_user',
                'affiliate_partner_id',
                'commission_rate',
                'is_public'
            ]);
        });
    }
};
