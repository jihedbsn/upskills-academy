<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Late Fee Log Table
     * 
     * Tracks every daily late fee charge applied by the cron job.
     * Provides an audit trail for all penalty charges.
     */
    public function up(): void
    {
        Schema::create('late_fee_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_specifique_id')->constrained('order_specifiques')->cascadeOnDelete();
            $table->foreignId('installment_id')->constrained('installments')->cascadeOnDelete();
            $table->decimal('fee_amount', 10, 2)->default(5.00);
            $table->date('charged_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['order_specifique_id', 'charged_date']);
            $table->index('installment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('late_fee_logs');
    }
};
