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
        Schema::create('certif_students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('certif_id');
            $table->unsignedBigInteger('student_success_id');
            $table->string('serial_number')->unique();
            $table->string('file_path')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->boolean('is_valid')->default(true);
            $table->timestamps();

            // Clés étrangères
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('certif_id')->references('id')->on('certifs')->onDelete('cascade');
            $table->foreign('student_success_id')->references('id')->on('student_successes')->onDelete('cascade');

            // Index
            $table->index(['student_id', 'product_id']);
            $table->index('serial_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certif_students');
    }
};
