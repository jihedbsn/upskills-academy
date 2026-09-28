<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This table manages affiliate partners and influencers who can create
     * coupons and earn commissions from successful referrals.
     */
    public function up(): void
    {
        Schema::create('affiliate_partners', function (Blueprint $table) {
            $table->id();

            // Partner identification
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->string('website')->nullable();

            // Contact and social information
            $table->string('contact_person')->nullable();
            $table->string('social_media_handle')->nullable();
            $table->json('social_links')->nullable(); // Instagram, YouTube, TikTok, etc.
            $table->text('bio')->nullable();

            // Commission settings
            $table->decimal('commission_rate', 5, 2)->default(10.00); // Default 10%
            $table->decimal('total_earnings', 10, 2)->default(0.00);
            $table->decimal('total_paid', 10, 2)->default(0.00);
            $table->decimal('pending_earnings', 10, 2)->default(0.00);

            // Performance metrics
            $table->integer('total_referrals')->default(0);
            $table->integer('successful_conversions')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0.00);
            $table->decimal('total_revenue_generated', 10, 2)->default(0.00);

            // Partner settings
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_approve_coupons')->default(false);
            $table->integer('max_coupon_uses')->nullable(); // Limit per coupon
            $table->decimal('max_commission_per_month', 10, 2)->nullable();

            // Agreement details
            $table->date('partnership_start_date')->nullable();
            $table->date('partnership_end_date')->nullable();
            $table->text('terms_agreed')->nullable();
            $table->timestamp('terms_agreed_at')->nullable();

            // Payment information
            $table->json('payment_details')->nullable(); // Bank info, PayPal, etc.
            $table->enum('payment_frequency', ['weekly', 'monthly', 'quarterly'])->default('monthly');
            $table->integer('payment_threshold')->default(50); // Minimum amount for payout

            // Tracking and analytics
            $table->string('referral_code')->unique()->nullable(); // Unique referral code
            $table->string('utm_source')->nullable(); // For tracking
            $table->json('tags')->nullable(); // Categories: influencer, blogger, corporate, etc.

            // Admin notes and status
            $table->text('admin_notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'suspended', 'terminated'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['email']);
            $table->index(['is_active', 'status']);
            $table->index(['referral_code']);
            $table->index(['partnership_start_date', 'partnership_end_date'], 'partnership_dates_idx');
            $table->index(['total_earnings']);
            $table->index(['commission_rate']);
            $table->index(['status', 'is_active'], 'status_active_idx');

            // Foreign key for admin who approved
            $table->foreign('approved_by')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_partners');
    }
};
