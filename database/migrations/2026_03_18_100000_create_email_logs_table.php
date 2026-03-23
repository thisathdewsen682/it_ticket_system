<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('mailable_class')->nullable();
            $table->string('subject')->nullable();
            $table->string('to');
            $table->string('cc')->nullable();
            $table->string('bcc')->nullable();
            $table->string('from');
            $table->string('status')->default('sent'); // sent, failed
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('ticket_id')->nullable();
            $table->timestamps();

            $table->index('mailable_class');
            $table->index('to');
            $table->index('status');
            $table->index('ticket_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
