<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_rewards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('referral_id');
            $table->enum('type', ['cash', 'credit'])->default('credit');
            $table->decimal('amount', 8, 2);
            $table->enum('role', ['referrer', 'referred'])->default('referrer');
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('referral_id')->references('id')->on('referrals')->onDelete('cascade');
            $table->index(['user_id', 'claimed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_rewards');
    }
};
