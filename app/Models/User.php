<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'despl_name',
        'email',
        'password',
        'google_id',
        'avatar',
        'gender',
        'country',
        'country_code',
        'mobile',
        'address_line_1',
        'address_line_2',
        'state',
        'district',
        'madhab_id',
        'mother_language',
        'profile_completed',
        'is_active',
        'can_check_approve',
        'otp',
        'otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'profile_completed' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function questions()
    {
        return $this->hasMany(QuestionAndAnswer::class, 'user_id');
    }

    public function madhab()
    {
        return $this->belongsTo(Madhab::class);
    }

    public function assignedQuestions()
    {
        return $this->hasMany(QuestionAndAnswer::class, 'assigned_mufti_id');
    }

    public function hasRole($roles)
    {
        if (is_string($roles)) {
            return $this->role?->name === $roles;
        }

        if (is_array($roles)) {
            return in_array($this->role?->name, $roles);
        }

        return false;
    }

    public function assignRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->role_id = $role->id;
            $this->save();
        }
        return $this;
    }

    public function scopeRole($query, $roleName)
    {
        return $query->whereHas('role', function($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }
}
