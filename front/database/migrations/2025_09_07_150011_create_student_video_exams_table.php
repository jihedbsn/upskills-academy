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
        Schema::create('student_video_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('lien');
            $table->text('video_description')->nullable();
            $table->integer('is_valid')->default(0); // 0: en cours, 1: success, -1: not success
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['student_id', 'product_id']);
            $table->index('is_valid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_video_exams');
    }
};
