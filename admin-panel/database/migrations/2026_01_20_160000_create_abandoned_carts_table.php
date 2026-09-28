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
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->integer('items_count')->default(0);
            $table->timestamp('abandoned_at');
            $table->timestamp('first_reminder_sent_at')->nullable();
            $table->timestamp('second_reminder_sent_at')->nullable();
            $table->timestamp('third_reminder_sent_at')->nullable();
            $table->boolean('converted')->default(false);
            $table->timestamp('converted_at')->nullable();
            $table->string('discount_coupon')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'converted']);
            $table->index('abandoned_at');
        });

        Schema::create('abandoned_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abandoned_cart_id')->constrained('abandoned_carts')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abandoned_cart_items');
        Schema::dropIfExists('abandoned_carts');
    }
};
