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
        Schema::create('zoom_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('zoom_meeting_id')->nullable()->comment('Zoom API meeting ID');
            $table->string('topic');
            $table->dateTime('start_time');
            $table->integer('duration')->comment('Duration in minutes');
            $table->string('timezone')->default('UTC');
            $table->string('password');
            $table->text('join_url')->nullable();
            $table->text('start_url')->nullable()->comment('Host start URL');
            $table->string('moderator_email');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->text('agenda')->nullable();
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('product_id');
            $table->index('start_time');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_meetings');
    }
};
