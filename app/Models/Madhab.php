<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Madhab extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($madhab) {
            if (!$madhab->slug) {
                $madhab->slug = Str::slug($madhab->name);
            }
        });
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function muftis()
    {
        return $this->belongsToMany(User::class, 'madhab_user');
    }
}
