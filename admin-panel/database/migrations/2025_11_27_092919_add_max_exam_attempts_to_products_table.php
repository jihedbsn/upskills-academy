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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('max_exam_attempts')->default(3)->after('prix')->comment('Maximum number of exam attempts allowed before requiring renewal');
            $table->decimal('renewal_price', 10, 2)->default(50.00)->after('max_exam_attempts')->comment('Price for exam renewal after exceeding attempts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['max_exam_attempts', 'renewal_price']);
        });
    }
};
