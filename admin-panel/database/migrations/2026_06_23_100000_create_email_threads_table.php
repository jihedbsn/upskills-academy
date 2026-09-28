<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_threads', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->string('subject_normalized');
            $table->string('participant_email');
            $table->string('participant_name')->nullable();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedInteger('messages_count')->default(0);
            $table->unsignedInteger('unread_count')->default(0);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();

            $table->index(['participant_email', 'subject_normalized']);
            $table->index('last_message_at');
            $table->index('unread_count');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_threads');
    }
};
