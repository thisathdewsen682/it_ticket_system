<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'employee_no')) {
                // Add as nullable first so existing rows don't break.
                $table->string('employee_no', 50)->nullable();
            }

            // For fresh installs, create_users_table already makes email nullable.
            // For existing MySQL installs, we alter email to be nullable below.
        });

        $driver = Schema::getConnection()->getDriverName();

        // SQLite can't easily ALTER column nullability without table rebuild.
        // Our base migration already handles it for fresh test DBs.
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        $databaseName = DB::getDatabaseName();

        // Backfill employee_no for existing users (must be unique and non-null).
        // Example format: EMP000123
        if (Schema::hasColumn('users', 'employee_no')) {
            DB::statement("UPDATE `users` SET `employee_no` = CONCAT('EMP', LPAD(`id`, 6, '0')) WHERE `employee_no` IS NULL");
        }

        // Enforce NOT NULL on employee_no.
        DB::statement('ALTER TABLE `users` MODIFY `employee_no` VARCHAR(50) NOT NULL');

        // Add unique index for employee_no if missing.
        $employeeNoIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', $databaseName)
            ->where('table_name', 'users')
            ->where('index_name', 'users_employee_no_unique')
            ->exists();

        if (!$employeeNoIndexExists) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('employee_no');
            });
        }

        // Make email nullable for existing databases, keeping uniqueness.
        // Safely drop/recreate unique index only if present.
        $emailIndexExists = DB::table('information_schema.statistics')
            ->where('table_schema', $databaseName)
            ->where('table_name', 'users')
            ->where('index_name', 'users_email_unique')
            ->exists();

        if ($emailIndexExists) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_email_unique');
            });
        }

        DB::statement('ALTER TABLE `users` MODIFY `email` VARCHAR(191) NULL');

        // Re-add unique index if missing.
        $emailIndexExistsAfter = DB::table('information_schema.statistics')
            ->where('table_schema', $databaseName)
            ->where('table_name', 'users')
            ->where('index_name', 'users_email_unique')
            ->exists();

        if (!$emailIndexExistsAfter) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('email');
            });
        }
    }

    public function down(): void
    {
        // Best-effort rollback.
        if (Schema::hasColumn('users', 'employee_no')) {
            Schema::table('users', function (Blueprint $table) {
                // Index name is typically users_employee_no_unique.
                $table->dropUnique('users_employee_no_unique');
                $table->dropColumn('employee_no');
            });
        }

        $driver = Schema::getConnection()->getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        // Restore email to NOT NULL (existing rows might violate this).
        DB::statement('ALTER TABLE `users` MODIFY `email` VARCHAR(191) NOT NULL');
    }
};
