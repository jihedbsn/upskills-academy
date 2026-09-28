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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->boolean('iscach')->default(false);
            $table->foreignId('categories_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('period')->nullable();
            $table->integer('point')->default(0);
            $table->string('video')->nullable();
            $table->string('image')->nullable();
            $table->integer('promo_points')->nullable();
            $table->string('langue', 10)->nullable();
            $table->string('statut')->nullable();
            $table->boolean('online')->default(false);
            $table->boolean('classroom')->default(false);
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->unsignedBigInteger('certif_id')->nullable();
            $table->string('type_course', 20)->nullable();
            $table->string('goverrnement')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->decimal('prix', 10, 2)->default(0);
            $table->boolean('is_exam_video')->default(false);
            $table->boolean('is_stage')->default(false);
            $table->boolean('is_classroom')->default(false);
            $table->boolean('is_zoom')->default(false);
            $table->boolean('is_online')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};


