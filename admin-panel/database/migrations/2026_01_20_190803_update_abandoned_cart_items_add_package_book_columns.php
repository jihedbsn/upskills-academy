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
        Schema::table('abandoned_cart_items', function (Blueprint $table) {
            // Make product_id nullable and add package_id and book_id
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->unsignedBigInteger('package_id')->nullable()->after('product_id');
            $table->unsignedBigInteger('book_id')->nullable()->after('package_id');

            // Add foreign keys for package and book
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abandoned_cart_items', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['package_id']);
            $table->dropForeign(['book_id']);

            // Drop columns
            $table->dropColumn(['package_id', 'book_id']);

            // Make product_id not nullable again
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });
    }
};
