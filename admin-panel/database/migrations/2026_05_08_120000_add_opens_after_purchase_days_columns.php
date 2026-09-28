<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Days after course access starts (purchase / access_granted_at) before a study or quiz unlocks.
     * Null keeps existing behavior for all rows.
     */
    public function up(): void
    {
        Schema::table('product_studies', function (Blueprint $table) {
            $table->unsignedInteger('opens_after_purchase_days')
                ->nullable()
                ->after('order')
                ->comment('Unlock N calendar days after access_granted_at; null = immediate');
        });

        Schema::table('quiz_products', function (Blueprint $table) {
            $table->unsignedInteger('opens_after_purchase_days')
                ->nullable()
                ->after('installment_month')
                ->comment('Unlock N calendar days after access_granted_at; null = no extra gate');
        });
    }

    public function down(): void
    {
        Schema::table('product_studies', function (Blueprint $table) {
            $table->dropColumn('opens_after_purchase_days');
        });

        Schema::table('quiz_products', function (Blueprint $table) {
            $table->dropColumn('opens_after_purchase_days');
        });
    }
};
