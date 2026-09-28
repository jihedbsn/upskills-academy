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
        Schema::table('web_masters', function (Blueprint $table) {
            // Add role_id column for new role system
            $table->foreignId('role_id')->nullable()->after('role')->constrained()->onDelete('set null');

            // Keep old role column for backward compatibility during transition
            // $table->dropColumn('role'); // Uncomment after data migration
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_masters', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
