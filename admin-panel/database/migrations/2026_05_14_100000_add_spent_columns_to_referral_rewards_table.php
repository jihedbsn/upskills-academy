<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referral_rewards', function (Blueprint $table) {
            $table->timestamp('spent_at')->nullable()->after('claimed_at');
            $table->unsignedBigInteger('spent_order_id')->nullable()->after('spent_at');
            $table->index(['user_id', 'spent_at']);
        });
    }

    public function down(): void
    {
        Schema::table('referral_rewards', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'spent_at']);
            $table->dropColumn(['spent_at', 'spent_order_id']);
        });
    }
};
