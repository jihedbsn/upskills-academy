<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('email_threads')->cascadeOnDelete();
            $table->enum('direction', ['inbound', 'outbound']);
            $table->string('from_email');
            $table->string('from_name')->nullable();
            $table->string('to_email');
            $table->string('subject');
            $table->longText('body')->nullable();
            $table->longText('body_html')->nullable();
            $table->string('message_id')->nullable()->unique();
            $table->string('in_reply_to')->nullable();
            $table->text('references')->nullable();
            $table->foreignId('email_log_id')->nullable()->constrained('email_logs')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['thread_id', 'created_at']);
            $table->index('in_reply_to');
            $table->index('direction');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_messages');
    }
};
