<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the foreign key constraint
        DB::statement('ALTER TABLE zoom_meetings DROP FOREIGN KEY zoom_meetings_created_by_foreign');

        // Make the column nullable
        DB::statement('ALTER TABLE zoom_meetings MODIFY created_by BIGINT UNSIGNED NULL');

        // Re-add the foreign key with SET NULL on delete
        DB::statement('ALTER TABLE zoom_meetings ADD CONSTRAINT zoom_meetings_created_by_foreign FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key
        DB::statement('ALTER TABLE zoom_meetings DROP FOREIGN KEY zoom_meetings_created_by_foreign');

        // Make the column non-nullable (this might fail if there are NULL values)
        DB::statement('ALTER TABLE zoom_meetings MODIFY created_by BIGINT UNSIGNED NOT NULL');

        // Re-add the foreign key with CASCADE on delete
        DB::statement('ALTER TABLE zoom_meetings ADD CONSTRAINT zoom_meetings_created_by_foreign FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE');
    }
};
