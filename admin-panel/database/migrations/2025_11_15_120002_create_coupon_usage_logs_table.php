<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This table provides comprehensive tracking and analytics for coupon usage.
     * It stores detailed information about every coupon application for reporting and analysis.
     */
    public function up(): void
    {
        Schema::create('coupon_usage_logs', function (Blueprint $table) {
            $table->id();

            // Core references
            $table->unsignedBigInteger('coupon_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('order_id')->nullable(); // Linked after order completion

            // Financial details
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('original_price', 10, 2);
            $table->decimal('final_price', 10, 2);
            $table->decimal('cart_total_before', 10, 2)->nullable();
            $table->decimal('cart_total_after', 10, 2)->nullable();

            // Usage timestamp
            $table->timestamp('used_at')->useCurrent();

            // Analytics fields for enhanced reporting
            $table->enum('user_type', ['new', 'returning', 'vip'])->nullable();
            $table->string('device_type', 50)->nullable(); // mobile, desktop, tablet
            $table->string('browser', 100)->nullable();
            $table->string('referral_source', 100)->nullable(); // direct, social, affiliate, etc.
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('currency', 3)->default('USD');

            // Coupon stacking information
            $table->boolean('was_stacked')->default(false);
            $table->json('stacked_with_coupons')->nullable(); // IDs of other coupons used in same order
            $table->integer('stack_order')->nullable(); // Order in which this coupon was applied

            // Additional context
            $table->json('products_purchased')->nullable(); // Product IDs and names
            $table->text('user_agent')->nullable();
            $table->json('session_data')->nullable(); // Additional session context if needed

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');

            // Indexes for analytics queries
            $table->index(['coupon_id', 'used_at']);
            $table->index(['student_id', 'used_at']);
            $table->index(['order_id']);
            $table->index(['user_type', 'used_at']);
            $table->index(['referral_source', 'used_at']);
            $table->index(['country_code', 'used_at']);
            $table->index(['was_stacked']);
            $table->index(['used_at']); // For time-based reports

            // Composite indexes for common queries
            $table->index(['coupon_id', 'student_id'], 'coupon_student_idx');
            $table->index(['used_at', 'user_type', 'referral_source'], 'analytics_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_usage_logs');
    }
};
