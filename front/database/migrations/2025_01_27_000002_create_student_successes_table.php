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
        Schema::create('student_successes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('student_id');
            $table->string('lien_video')->nullable();
            $table->integer('success')->default(0); // 0: en attente, 1: success, -1: refuse
            $table->text('admin_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            // Clés étrangères
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            // Index unique pour éviter les doublons
            $table->unique(['product_id', 'student_id']);
            
            // Index pour les performances
            $table->index(['product_id', 'success']);
            $table->index(['student_id', 'success']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_successes');
    }
};
