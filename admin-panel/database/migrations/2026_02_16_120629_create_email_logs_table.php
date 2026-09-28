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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('student_email');
            $table->string('student_name')->nullable();
            $table->string('email_type');          // e.g. 'zoom_meeting', 'course_session', 'certificate', etc.
            $table->string('subject');
            $table->string('status')->default('sent'); // sent, failed
            $table->text('error_message')->nullable();
            $table->string('related_model')->nullable(); // e.g. 'ZoomMeeting', 'CourseSession'
            $table->unsignedBigInteger('related_id')->nullable();
            $table->timestamps();

            $table->index(['email_type']);
            $table->index(['status']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
