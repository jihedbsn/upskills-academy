<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include 'awaiting_payment' status
        DB::statement("ALTER TABLE installments MODIFY COLUMN status ENUM('pending', 'paid', 'overdue', 'awaiting_payment') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE installments MODIFY COLUMN status ENUM('pending', 'paid', 'overdue') NOT NULL DEFAULT 'pending'");
    }
};
