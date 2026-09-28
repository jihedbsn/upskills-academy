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
        // Customer points balance table
        Schema::create('customer_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->integer('total_points')->default(0);
            $table->integer('available_points')->default(0); // Points that can be used
            $table->integer('used_points')->default(0);
            $table->timestamps();

            $table->unique('student_id');
            $table->index('available_points');
        });

        // Points transactions log
        Schema::create('points_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->enum('type', ['earn', 'redeem', 'adjust', 'expire'])->default('earn');
            $table->integer('points'); // Positive for earn, negative for redeem
            $table->decimal('amount', 10, 2)->nullable(); // Dollar amount related to transaction
            $table->string('description')->nullable();
            $table->integer('balance_after')->default(0); // Balance after this transaction
            $table->timestamps();

            $table->index(['student_id', 'type']);
            $table->index('created_at');
        });

        // Add points discount column to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('points_used')->nullable()->after('price');
            $table->decimal('points_discount', 10, 2)->nullable()->after('points_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['points_used', 'points_discount']);
        });
        Schema::dropIfExists('points_transactions');
        Schema::dropIfExists('customer_points');
    }
};
