<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approval_user_id')->constrained('users')->restrictOnDelete();

            $table->string('title');
            $table->text('description');

            $table->string('category', 50);
            $table->string('priority', 20);
            $table->dateTime('needed_by')->nullable();

            $table->string('affected_user')->nullable();
            $table->string('location')->nullable();
            $table->string('asset_tag', 100)->nullable();
            $table->string('device_name', 100)->nullable();
            $table->string('ip_address', 45)->nullable();

            $table->string('system_name')->nullable();
            $table->string('access_role')->nullable();
            $table->date('access_start_date')->nullable();
            $table->date('access_end_date')->nullable();

            $table->dateTime('incident_started_at')->nullable();
            $table->text('steps_to_reproduce')->nullable();
            $table->text('error_message')->nullable();
            $table->text('impact')->nullable();

            $table->string('attachment_path')->nullable();
            $table->string('attachment_original_name')->nullable();

            $table->string('status', 20)->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};