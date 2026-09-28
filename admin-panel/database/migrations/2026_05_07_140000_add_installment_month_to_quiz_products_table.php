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
        Schema::table('quiz_products', function (Blueprint $table) {
            $table->unsignedInteger('installment_month')
                ->nullable()
                ->after('use_own_questions')
                ->comment('Month number for installment-based quiz unlock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_products', function (Blueprint $table) {
            $table->dropColumn('installment_month');
        });
    }
};
