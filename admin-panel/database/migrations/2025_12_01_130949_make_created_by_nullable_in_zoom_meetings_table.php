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
        Schema::table('zoom_meetings', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['created_by']);

            // Modify the column to be nullable
            $table->foreignId('created_by')->nullable()->change()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zoom_meetings', function (Blueprint $table) {
            // Drop the nullable foreign key
            $table->dropForeign(['created_by']);

            // Restore the original non-nullable foreign key
            $table->foreignId('created_by')->nullable(false)->change()->constrained('users')->onDelete('cascade');
        });
    }
};
