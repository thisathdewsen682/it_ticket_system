<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'employee_no',
        'email',
        'password',
        'role_id',
        'is_super_admin',
        'force_password_change',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'force_password_change' => 'boolean',
        ];
    }

    // Primary role relationship (backward compatibility)
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Many-to-many roles relationship
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withPivot('section_id')
            ->withTimestamps();
    }

    // Helper methods for role checking
    
    /**
     * Check if user has a specific role (by name)
     */
    public function hasRole(string $roleName): bool
    {
        // Check primary role first
        if ($this->role && $this->role->name === $roleName) {
            return true;
        }
        
        // Check additional roles
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if user has a specific role for a specific section
     */
    public function hasRoleInSection(string $roleName, int $sectionId): bool
    {
        return $this->roles()
            ->where('name', $roleName)
            ->wherePivot('section_id', $sectionId)
            ->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleNames): bool
    {
        // Check primary role
        if ($this->role && in_array($this->role->name, $roleNames)) {
            return true;
        }
        
        // Check additional roles
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    /**
     * Get all sections where user has a specific role
     */
    public function getSectionsForRole(string $roleName): \Illuminate\Support\Collection
    {
        return $this->roles()
            ->where('name', $roleName)
            ->whereNotNull('role_user.section_id')
            ->get()
            ->pluck('pivot.section_id')
            ->unique();
    }

    /**
     * Get all role names (including primary and additional)
     */
    public function getAllRoleNames(): array
    {
        $roleNames = [];
        
        if ($this->role) {
            $roleNames[] = $this->role->name;
        }
        
        $additionalRoles = $this->roles->pluck('name')->toArray();
        
        return array_unique(array_merge($roleNames, $additionalRoles));
    }
}