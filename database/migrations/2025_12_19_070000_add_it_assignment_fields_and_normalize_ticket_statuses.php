<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tickets')) {
            return;
        }

        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'it_member_id')) {
                $table->foreignId('it_member_id')
                    ->nullable()
                    ->after('approval_user_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('tickets', 'it_due_at')) {
                $table->dateTime('it_due_at')->nullable()->after('needed_by');
            }

            if (!Schema::hasColumn('tickets', 'it_instructions')) {
                $table->text('it_instructions')->nullable()->after('location');
            }
        });

        // Normalize older status values (including truncated MySQL values) to short codes.
        DB::table('tickets')
            ->where('status', 'approved_by_department_manager')
            ->orWhere('status', 'like', 'approved_by_department%')
            ->update(['status' => 'dept_approved']);

        DB::table('tickets')
            ->where('status', 'rejected_by_department_manager')
            ->orWhere('status', 'like', 'rejected_by_department%')
            ->update(['status' => 'dept_rejected']);
    }

    public function down(): void
    {
        // Kept minimal to avoid failures on existing data.
    }
};
