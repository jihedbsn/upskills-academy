<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This table tracks commission calculations and payments for affiliate partners.
     * Each successful coupon usage generates a commission record.
     */
    public function up(): void
    {
        Schema::create('partner_commissions', function (Blueprint $table) {
            $table->id();

            // Partner and usage references
            $table->unsignedBigInteger('partner_id');
            $table->unsignedBigInteger('coupon_usage_id');
            $table->unsignedBigInteger('order_id')->nullable();

            // Financial details
            $table->decimal('order_total', 10, 2);
            $table->decimal('commission_rate', 5, 2); // Rate at time of calculation
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->nullable(); // How much customer saved

            // Commission calculation details
            $table->decimal('base_amount', 10, 2)->nullable(); // Amount commission calculated on
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('fee_amount', 10, 2)->default(0.00); // Platform fees
            $table->decimal('net_commission', 10, 2); // Final amount to pay partner

            // Status and tracking
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled', 'disputed'])->default('pending');
            $table->text('status_reason')->nullable(); // Reason for cancellation/dispute

            // Timestamps for workflow
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Payment processing
            $table->string('payment_batch_id')->nullable(); // For batch payments
            $table->string('payment_reference')->nullable(); // External payment ID
            $table->string('payment_method')->nullable(); // bank_transfer, paypal, etc.
            $table->json('payment_details')->nullable(); // Additional payment info

            // Admin tracking
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable();
            $table->text('admin_notes')->nullable();

            // Currency and conversion (for international partners)
            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 10, 6)->default(1.000000);
            $table->decimal('original_amount', 10, 2)->nullable(); // In partner's currency

            // Dispute handling
            $table->timestamp('dispute_opened_at')->nullable();
            $table->text('dispute_reason')->nullable();
            $table->timestamp('dispute_resolved_at')->nullable();

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('partner_id')->references('id')->on('affiliate_partners')->onDelete('cascade');
            $table->foreign('coupon_usage_id')->references('id')->on('coupon_usage_logs')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('paid_by')->references('id')->on('admins')->onDelete('set null');

            // Indexes for performance and reporting
            $table->index(['partner_id', 'status']);
            $table->index(['partner_id', 'calculated_at']);
            $table->index(['status', 'calculated_at']);
            $table->index(['payment_batch_id']);
            $table->index(['approved_at']);
            $table->index(['paid_at']);
            $table->index(['coupon_usage_id']);
            $table->index(['order_id']);

            // Composite indexes for common queries
            $table->index(['partner_id', 'status', 'calculated_at'], 'partner_status_date_idx');
            $table->index(['status', 'approved_at', 'paid_at'], 'payment_workflow_idx');

            // Unique constraint to prevent duplicate commissions
            $table->unique(['partner_id', 'coupon_usage_id'], 'unique_partner_usage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_commissions');
    }
};
