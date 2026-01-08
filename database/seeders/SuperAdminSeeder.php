<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set the first user (ID 6) as super admin
        $user = User::find(6);

        if ($user) {
            $user->is_super_admin = true;
            $user->save();

            $this->command->info("User '{$user->name}' (ID: {$user->id}) is now a Super Admin!");
        } else {
            $this->command->warn("User with ID 6 not found!");
        }
    }
}
