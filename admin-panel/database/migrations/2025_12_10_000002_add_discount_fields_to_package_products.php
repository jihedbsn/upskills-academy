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
        Schema::table('package_products', function (Blueprint $table) {
            // Add type field to distinguish between percentage and fixed amount
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage')->after('valeur_reduction');
            // Add field for fixed discount amount
            $table->decimal('fixed_discount', 8, 2)->nullable()->after('discount_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_products', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'fixed_discount']);
        });
    }
};
