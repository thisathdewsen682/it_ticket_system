<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role; // <-- add this

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // ...existing code...

        Role::insert([
            ['name' => 'employee'],
            ['name' => 'dept_manager'],
            ['name' => 'section_manager'],
            ['name' => 'it_manager'],
        ]);

        // ...existing code...
    }
}