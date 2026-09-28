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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'comment', 'rating', 'purchase', 'ticket', 'evaluation', etc.
            $table->string('notifiable_type'); // 'Admin', 'Student', etc.
            $table->unsignedBigInteger('notifiable_id'); // ID of the user receiving the notification
            $table->text('data'); // JSON data with notification details
            $table->string('title'); // Notification title
            $table->text('message'); // Notification message
            $table->string('action_url')->nullable(); // URL to navigate when clicked
            $table->string('icon')->nullable(); // Icon class or emoji
            $table->string('color')->default('blue'); // 'blue', 'red', 'green', 'yellow', 'purple'
            $table->timestamp('read_at')->nullable(); // When notification was read
            $table->boolean('is_important')->default(false); // For urgent notifications (evaluation)
            $table->timestamps();

            // Indexes
            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index('type');
            $table->index('read_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
