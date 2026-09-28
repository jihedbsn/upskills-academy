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
        // Mettre à jour l'enum pour inclure le nouveau statut
        DB::statement("ALTER TABLE chat_conversations MODIFY COLUMN status ENUM('active', 'closed', 'transferred_to_admin') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à l'ancien enum
        DB::statement("ALTER TABLE chat_conversations MODIFY COLUMN status ENUM('active', 'closed') DEFAULT 'active'");
    }
};
