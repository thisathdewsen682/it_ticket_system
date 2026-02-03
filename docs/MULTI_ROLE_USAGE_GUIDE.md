# Multi-Role System Usage Guide

## Overview
Users can now have multiple roles with section-specific permissions while maintaining backward compatibility with the existing single-role system.

## Database Structure

### Tables
- `users` - Has `role_id` (primary role, backward compatible)
- `roles` - Available roles
- `role_user` - Pivot table for many-to-many relationships
- `sections` - Organizational sections

### Pivot Table Structure
```
role_user:
- user_id
- role_id
- section_id (nullable - for section-specific roles)
```

## How It Works

### Backward Compatibility
- Users still have a primary `role_id` in the users table
- All existing code checking `$user->role` will continue to work
- New many-to-many system is additive, not replacing

### Assigning Multiple Roles

#### 1. Assign a role without section (global role)
```php
$user = User::find(1);
$role = Role::where('name', 'it-dept-manager')->first();

// Attach role without section
$user->roles()->attach($role->id);
```

#### 2. Assign a role for a specific section
```php
$user = User::find(1);
$role = Role::where('name', 'dept-manager')->first();
$section = Section::where('name', 'HR')->first();

// Attach role for HR section
$user->roles()->attach($role->id, ['section_id' => $section->id]);

// Attach same role for another section
$section2 = Section::where('name', 'FINANCE')->first();
$user->roles()->attach($role->id, ['section_id' => $section2->id]);
```

#### 3. Using syncWithPivotValues (recommended for bulk assignment)
```php
$user = User::find(1);
$deptManagerRole = Role::where('name', 'dept-manager')->first();

// Assign dept-manager role for multiple sections
$hrSection = Section::where('name', 'HR')->first();
$financeSection = Section::where('name', 'FINANCE')->first();

$user->roles()->attach([
    $deptManagerRole->id => ['section_id' => $hrSection->id],
    $deptManagerRole->id => ['section_id' => $financeSection->id],
]);
```

## Helper Methods

### 1. Check if user has a role (any context)
```php
if ($user->hasRole('it-dept-manager')) {
    // User has IT dept manager role
}

if ($user->hasRole('dept-manager')) {
    // User has dept manager role (for at least one section)
}
```

### 2. Check if user has role for specific section
```php
$hrSection = Section::where('name', 'HR')->first();

if ($user->hasRoleInSection('dept-manager', $hrSection->id)) {
    // User is dept manager for HR section
}
```

### 3. Check if user has any of multiple roles
```php
if ($user->hasAnyRole(['dept-manager', 'it-dept-manager', 'admin'])) {
    // User has at least one of these roles
}
```

### 4. Get all sections where user has a specific role
```php
$sections = $user->getSectionsForRole('dept-manager');
// Returns collection of section IDs where user is dept-manager
```

### 5. Get all role names
```php
$roleNames = $user->getAllRoleNames();
// Returns array: ['requester', 'dept-manager', 'it-dept-manager']
// Includes both primary role and additional roles
```

## Real-World Example Scenarios

### Scenario 1: User is Department Manager of Two Departments
```php
// John is HR Manager and Finance Manager
$john = User::find(5);
$deptManagerRole = Role::where('name', 'dept-manager')->first();
$hrSection = Section::where('name', 'HR')->first();
$financeSection = Section::where('name', 'FINANCE')->first();

// Assign both
$john->roles()->attach([
    $deptManagerRole->id => ['section_id' => $hrSection->id],
    $deptManagerRole->id => ['section_id' => $financeSection->id],
]);

// Now check permissions
if ($john->hasRoleInSection('dept-manager', $hrSection->id)) {
    // John can approve HR tickets
}

if ($john->hasRoleInSection('dept-manager', $financeSection->id)) {
    // John can approve Finance tickets
}
```

### Scenario 2: User is Both Department Manager and IT Department Manager
```php
// Sarah is IT Dept Manager (global) + HR Dept Manager (section-specific)
$sarah = User::find(10);
$itDeptManagerRole = Role::where('name', 'it-dept-manager')->first();
$deptManagerRole = Role::where('name', 'dept-manager')->first();
$hrSection = Section::where('name', 'HR')->first();

// Assign IT Dept Manager (no section - global)
$sarah->roles()->attach($itDeptManagerRole->id);

// Assign HR Dept Manager (section-specific)
$sarah->roles()->attach($deptManagerRole->id, ['section_id' => $hrSection->id]);

// Check permissions
if ($sarah->hasRole('it-dept-manager')) {
    // Sarah can manage IT department tasks
}

if ($sarah->hasRoleInSection('dept-manager', $hrSection->id)) {
    // Sarah can also approve HR tickets
}
```

## Middleware & Authorization Example

```php
// In your controller or middleware
public function approveDeptTicket(Request $request, Ticket $ticket)
{
    $user = auth()->user();
    
    // Check if user is dept manager for this ticket's section
    if (!$user->hasRoleInSection('dept-manager', $ticket->section_id)) {
        abort(403, 'You are not authorized to approve tickets for this section');
    }
    
    // Proceed with approval
    // ...
}
```

## Migration from Old System

Your existing data is preserved:
- Users with `role_id` set will continue to work exactly as before
- Use `$user->role` to access primary role (backward compatible)
- Use `$user->roles` to access additional roles (new feature)

You can gradually migrate users to the new system:
```php
// Keep primary role intact
$user->role_id; // Still works

// Add additional roles as needed
$user->roles()->attach($additionalRole->id);
```

## Best Practices

1. **Primary Role**: Keep using `role_id` for user's main role
2. **Additional Roles**: Use pivot table for section-specific or additional roles
3. **Performance**: Use eager loading when checking multiple users:
   ```php
   $users = User::with(['role', 'roles.pivot'])->get();
   ```

4. **Validation**: The unique constraint prevents duplicate role-section assignments:
   ```
   unique(['user_id', 'role_id', 'section_id'])
   ```

## Adding the IT Department Manager Role

```php
// In your seeder or tinker
Role::create(['name' => 'it-dept-manager']);

// Assign to a user
$user = User::find(1);
$role = Role::where('name', 'it-dept-manager')->first();
$user->roles()->attach($role->id);
```
