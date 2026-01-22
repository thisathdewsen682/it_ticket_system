<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sections')) {
            Schema::create('sections', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
            });
        }

        // Seed default sections
        $sections = [
            'ADMIN',
            'FA',
            'FAQC',
            'FCS',
            'FINANCE',
            'HR',
            'IT',
            'QC',
            'YD',
            'ISO',
            'ENG',
            'LOGISTICS',
        ];

        foreach ($sections as $name) {
            DB::table('sections')->updateOrInsert(['name' => $name], ['name' => $name]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
