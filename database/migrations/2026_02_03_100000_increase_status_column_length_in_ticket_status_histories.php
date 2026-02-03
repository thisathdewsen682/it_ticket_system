<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_status_histories', function (Blueprint $table) {
            $table->string('from_status', 50)->nullable()->change();
            $table->string('to_status', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_status_histories', function (Blueprint $table) {
            $table->string('from_status', 20)->nullable()->change();
            $table->string('to_status', 20)->change();
        });
    }
};
