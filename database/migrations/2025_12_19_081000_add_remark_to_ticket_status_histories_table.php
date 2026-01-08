<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ticket_status_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_status_histories', 'remark')) {
                $table->text('remark')->nullable()->after('to_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ticket_status_histories', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_status_histories', 'remark')) {
                $table->dropColumn('remark');
            }
        });
    }
};
