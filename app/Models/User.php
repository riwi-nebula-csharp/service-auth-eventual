<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'provider',
        'provider_id',
        'avatar_url',
        'phone',
        'role',
        'status',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function portalPermissions()
    {
        return $this->hasMany(PortalPermission::class);
    }

    public function passwordResetTokens()
    {
        return $this->hasMany(PasswordResetToken::class);
    }

    // Scopes útiles
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeEmployees($query)
    {
        return $query->where('role', 'employee');
    }
}