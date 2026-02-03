<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;

// Create or get the IT Department Manager role
$itDeptManagerRole = Role::firstOrCreate(['name' => 'it-dept-manager']);
echo "IT Dept Manager Role ID: {$itDeptManagerRole->id}\n";

// Get user 8 (Kumara)
$user = User::find(8);

if (!$user) {
    echo "User not found!\n";
    exit(1);
}

echo "User: {$user->name} (ID: {$user->id})\n";
echo "Primary Role: {$user->role->name}\n\n";

// Check if user already has this role
$hasRole = $user->roles()->where('role_id', $itDeptManagerRole->id)->whereNull('section_id')->exists();

if (!$hasRole) {
    // Assign the it-dept-manager role (global - no section)
    $user->roles()->attach($itDeptManagerRole->id);
    echo "✓ Successfully assigned 'it-dept-manager' role to {$user->name}\n\n";
} else {
    echo "✓ User already has 'it-dept-manager' role\n\n";
}

// Reload to get fresh data
$user = User::with('role', 'roles')->find(8);

// Display all roles
echo "=== All Roles for {$user->name} ===\n";
echo "Primary Role: {$user->role->name}\n";
echo "\nAdditional Roles:\n";
foreach ($user->roles as $role) {
    $section = $role->pivot->section_id ? " (Section ID: {$role->pivot->section_id})" : " (Global)";
    echo "  - {$role->name}{$section}\n";
}

// Test helper methods
echo "\n=== Permission Checks ===\n";
echo "hasRole('section_manager'): " . ($user->hasRole('section_manager') ? 'YES' : 'NO') . "\n";
echo "hasRole('it-dept-manager'): " . ($user->hasRole('it-dept-manager') ? 'YES' : 'NO') . "\n";
echo "hasAnyRole(['it-dept-manager', 'admin']): " . ($user->hasAnyRole(['it-dept-manager', 'admin']) ? 'YES' : 'NO') . "\n";

echo "\nAll role names: " . implode(', ', $user->getAllRoleNames()) . "\n";
