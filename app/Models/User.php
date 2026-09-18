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
        'dob',
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
        'login_count',
        'last_login_at',
        'last_seen_at',
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
            'dob' => 'date',
            'password' => 'hashed',
            'profile_completed' => 'boolean',
            'is_active' => 'boolean',
            'can_check_approve' => 'boolean',
            'login_count' => 'integer',
            'last_login_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public function recordLogin(): void
    {
        $this->increment('login_count');
        $this->updateQuietly([
            'last_login_at' => now(),
            'last_seen_at'  => now(),
        ]);
    }

    public function isOnline(int $minutes = 5): bool
    {
        if (!$this->last_seen_at) {
            return false;
        }
        return $this->last_seen_at->greaterThanOrEqualTo(now()->subMinutes($minutes));
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
        $roleName = strtolower(trim($this->role?->name ?? ''));

        if (is_string($roles)) {
            $r = strtolower(trim($roles));
            if ($r === 'user' || $r === 'questioner') {
                return in_array($roleName, ['user', 'questioner']);
            }
            return $roleName === $r;
        }

        if (is_array($roles)) {
            $rolesList = array_map(fn($item) => strtolower(trim($item)), $roles);
            if (in_array('user', $rolesList) || in_array('questioner', $rolesList)) {
                if (in_array($roleName, ['user', 'questioner'])) {
                    return true;
                }
            }
            return in_array($roleName, $rolesList);
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
