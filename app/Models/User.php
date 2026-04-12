<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
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
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function madhab()
    {
        return $this->belongsTo(Madhab::class);
    }

    public function madhabs()
    {
        return $this->belongsToMany(Madhab::class, 'madhab_user');
    }

    public function assignedQuestions()
    {
        return $this->hasMany(Question::class, 'assigned_mufti_id');
    }

    public function muftiAnswers()
    {
        return $this->hasMany(MuftiAnswer::class, 'mufti_id');
    }

    public function publishedFatwas()
    {
        return $this->hasMany(Fatwa::class, 'published_by');
    }
}
