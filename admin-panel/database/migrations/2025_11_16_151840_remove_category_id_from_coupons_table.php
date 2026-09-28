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
            // Drop the foreign key constraint first
            $table->dropForeign(['category_id']);
            // Drop the index
            $table->dropIndex(['category_id']);
            // Drop the column
            $table->dropColumn('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            // Re-add the category_id column
            $table->unsignedBigInteger('category_id')->nullable()->after('affiliate_partner_id');
            // Re-add the foreign key constraint
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            // Re-add the index
            $table->index('category_id');
        });
    }
};
