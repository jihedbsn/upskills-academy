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
        Schema::table('tache_planifies', function (Blueprint $table) {
            $table->string('is_send')->nullable()->after('notes')->comment('Status of email sending: null=not sent, sent=email sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tache_planifies', function (Blueprint $table) {
            $table->dropColumn('is_send');
        });
    }
};
