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
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('set null');
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->enum('status', ['active', 'admin_taken', 'closed'])->default('active');
            $table->boolean('admin_takeover')->default(false);
            $table->timestamp('admin_takeover_at')->nullable();
            $table->string('visitor_ip')->nullable();
            $table->string('visitor_country')->nullable();
            $table->string('visitor_language', 10)->default('en');
            $table->text('last_message')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->integer('unread_admin_count')->default(0);
            $table->integer('unread_user_count')->default(0);
            $table->timestamps();

            $table->index(['status', 'updated_at']);
            $table->index(['admin_takeover', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
