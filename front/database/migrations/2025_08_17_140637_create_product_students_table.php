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
        Schema::create('product_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->boolean('is_active')->default(true);
            $table->timestamp('access_granted_at')->nullable();
            $table->timestamps();
            
            // Contrainte unique : un étudiant ne peut acheter un cours qu'une seule fois
            $table->unique(['product_id', 'student_id']);
            
            // Index pour optimiser les requêtes
            $table->index(['student_id', 'is_active']);
            $table->index(['product_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_students');
    }
};
