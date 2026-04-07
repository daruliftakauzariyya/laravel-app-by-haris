<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

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
        'madhab',
        'mother_language',
        'profile_completed',
        'is_active',
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
