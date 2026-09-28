<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add installment-related fields to order_specifiques table:
     * - is_suspended: Whether the student's access is suspended due to overdue payment
     * - suspended_at: When the suspension occurred
     * - late_fee_total: Accumulated late fees ($5/day)
     * - payment_type: How the order was created (admin_created or student_checkout)
     */
    public function up(): void
    {
        Schema::table('order_specifiques', function (Blueprint $table) {
            $table->boolean('is_suspended')->default(false)->after('notes');
            $table->timestamp('suspended_at')->nullable()->after('is_suspended');
            $table->decimal('late_fee_total', 10, 2)->default(0)->after('suspended_at');
            $table->enum('payment_type', ['admin_created', 'student_checkout'])->default('admin_created')->after('late_fee_total');
        });
    }

    public function down(): void
    {
        Schema::table('order_specifiques', function (Blueprint $table) {
            $table->dropColumn(['is_suspended', 'suspended_at', 'late_fee_total', 'payment_type']);
        });
    }
};
