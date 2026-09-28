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
        Schema::create('course_extension_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('price', 10, 2);
            $table->string('payment_method')->nullable(); // credit_card, bank_transfer
            $table->string('payment_status')->default('pending'); // pending, approved, rejected
            $table->boolean('payment_success')->default(false);
            $table->string('payment_receipt')->nullable();
            $table->string('payment_description')->nullable();
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_payment_intent')->nullable();
            $table->string('transaction_id')->nullable();
            $table->integer('extension_months')->default(0); // how many months to extend
            $table->datetime('old_expiration_date')->nullable();
            $table->datetime('new_expiration_date')->nullable();
            $table->boolean('is_processed')->default(false); // whether extension was applied
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['student_id', 'product_id']);
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_extension_orders');
    }
};
