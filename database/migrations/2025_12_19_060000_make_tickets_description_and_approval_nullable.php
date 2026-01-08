<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tickets')) {
            return;
        }

        $driver = DB::connection()->getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            // These statements are MySQL/MariaDB-specific (ALTER TABLE ... MODIFY / DROP FOREIGN KEY)
            // and will fail on SQLite (used in tests) and other drivers.
            return;
        }

        // Make description nullable
        DB::statement('ALTER TABLE `tickets` MODIFY `description` TEXT NULL');

        // Make approval_user_id nullable (requires dropping & recreating FK)
        DB::statement('ALTER TABLE `tickets` DROP FOREIGN KEY `tickets_approval_user_id_foreign`');
        DB::statement('ALTER TABLE `tickets` MODIFY `approval_user_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `tickets` ADD CONSTRAINT `tickets_approval_user_id_foreign` FOREIGN KEY (`approval_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL');
    }

    public function down(): void
    {
        // Down migration intentionally left minimal; making these NOT NULL again can fail
        // if existing rows contain NULLs.
    }
};
