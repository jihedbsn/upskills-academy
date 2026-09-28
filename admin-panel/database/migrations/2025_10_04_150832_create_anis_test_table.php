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
        Schema::create('anis_test', function (Blueprint $table) {
            $table->id();
            $table->string('task_name');
            $table->text('task_description');
            $table->datetime('scheduled_time');
            $table->datetime('actual_created_time');
            $table->string('status')->default('pending');
            $table->string('email_sent')->default('no');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['scheduled_time']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anis_test');
    }
};