<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('code', 20)->unique();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('students')->onDelete('cascade');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_codes');
    }
};
