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
        Schema::create('partnership_courses', function (Blueprint $table) {
            $table->id();
            $table->string('institution_name');
            $table->text('institution_address');
            $table->string('phone');
            $table->string('email');
            $table->string('course_title_en');
            $table->enum('course_type', ['training_course', 'workshop']);
            $table->enum('delivery_method', ['classroom', 'online']);
            $table->enum('evaluation_method', ['self_assessment', 'theoretical_test', 'practical_test', 'theoretical_practical_test']);
            $table->text('course_description');
            $table->string('website_url')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partnership_courses');
    }
};
