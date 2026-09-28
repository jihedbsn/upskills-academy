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
        Schema::create('training_case_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_case_id')->constrained('training_cases')->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->nullable(); // pdf, image, etc
            $table->integer('file_size')->nullable(); // in bytes
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('training_case_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_case_files');
    }
};
