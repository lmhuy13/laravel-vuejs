<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'team_id', 'role_id');
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }
}
