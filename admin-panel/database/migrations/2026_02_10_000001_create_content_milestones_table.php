<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Content Milestones Table
     * 
     * Links course content (studies/lectures) to specific installment months.
     * This enables the Drip Content system where content is unlocked
     * progressively as installments are paid.
     * 
     * Example: A 3-month course has 3 milestones:
     *   - Month 1 lectures → milestone_month = 1
     *   - Month 2 lectures → milestone_month = 2
     *   - Month 3 lectures → milestone_month = 3
     */
    public function up(): void
    {
        Schema::create('content_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_study_id')->constrained('product_studies')->cascadeOnDelete();
            $table->unsignedInteger('milestone_month');
            $table->timestamps();

            $table->unique(['product_id', 'product_study_id'], 'content_milestone_unique');
            $table->index(['product_id', 'milestone_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_milestones');
    }
};
