<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];
    
    // One-to-many relationship (backward compatibility)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Many-to-many relationship
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->withPivot('section_id')
            ->withTimestamps();
    }
}