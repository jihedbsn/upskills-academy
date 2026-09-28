<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referrer_id');
            $table->unsignedBigInteger('referred_id');
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->decimal('reward_amount', 8, 2)->default(10.00);
            $table->unsignedBigInteger('completed_order_id')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('referrer_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('referred_id')->references('id')->on('students')->onDelete('cascade');
            $table->unique('referred_id');
            $table->index(['referrer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
