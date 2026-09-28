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
            // Add minimum cart items - if null or 0, coupon is valid for any number of items
            // Default is 1 (requires at least 1 item in cart)
            $table->integer('min_cart_items')->default(1)->after('min_items');

            // Add index for performance
            $table->index(['min_cart_items']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            // Drop index
            $table->dropIndex(['min_cart_items']);

            // Drop column
            $table->dropColumn('min_cart_items');
        });
    }
};
