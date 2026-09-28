<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This table replaces session-based coupon storage for security.
     * It stores temporarily applied coupons until checkout completion.
     */
    public function up(): void
    {
        Schema::create('active_cart_coupons', function (Blueprint $table) {
            $table->id();

            // Session and user identification
            $table->string('session_id', 255)->index();
            $table->unsignedBigInteger('student_id')->nullable();

            // Coupon reference
            $table->unsignedBigInteger('coupon_id');

            // Cart and discount details
            $table->decimal('cart_total', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('original_cart_total', 10, 2)->nullable(); // Before discount

            // Timestamps
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamp('expires_at')->nullable(); // Auto-cleanup expired coupons
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

            // Indexes for performance
            $table->index(['session_id', 'student_id']);
            $table->index(['coupon_id', 'session_id']);
            $table->index(['expires_at']);
            $table->index(['applied_at']);

            // Unique constraint to prevent duplicate coupon applications per session
            $table->unique(['session_id', 'coupon_id'], 'unique_session_coupon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_cart_coupons');
    }
};
