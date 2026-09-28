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
        Schema::create('admin_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_response_id')->constrained('message_responses')->onDelete('cascade');
            $table->unsignedBigInteger('admin_id');
            $table->text('response_body');
            $table->json('response_attachments')->nullable(); // Store file paths as JSON array
            $table->timestamps();

            $table->index('message_response_id');
            $table->index('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_responses');
    }
};
