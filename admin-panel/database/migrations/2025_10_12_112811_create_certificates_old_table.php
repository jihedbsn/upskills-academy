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
        Schema::create('certificates_old', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name_ar', 1000)->nullable();
            $table->string('name_en', 1000)->nullable();
            $table->string('lecturer_ar', 1000)->nullable();
            $table->string('lecturer_en', 1000)->nullable();
            $table->string('description_ar', 255)->nullable();
            $table->string('description_en', 255)->nullable();
            $table->integer('qrcodex')->nullable();
            $table->integer('qrcodey')->nullable();
            $table->string('image', 100);
            $table->integer('image_width')->default(1000);
            $table->integer('image_height')->default(1000);
            $table->integer('image_real_height')->default(1000);
            $table->tinyInteger('confirmed')->default(1);
            $table->string('date', 35);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates_old');
    }
};
