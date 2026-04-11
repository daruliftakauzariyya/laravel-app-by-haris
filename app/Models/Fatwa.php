<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\FatwaUserView;
use App\Models\FatwaLike;

class Fatwa extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'mufti_answer_id',
        'published_by',
        'checked_approved_by',
        'featured_image',
        'views',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function fatwaLikes()
    {
        return $this->hasMany(FatwaLike::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function muftiAnswer()
    {
        return $this->belongsTo(MuftiAnswer::class);
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function verifiers()
    {
        return $this->belongsToMany(User::class, 'fatwa_verifier_user');
    }

    public function checkedApprovedBy()
    {
        return $this->belongsTo(User::class, 'checked_approved_by');
    }

    public function userViews()
    {
        return $this->hasMany(FatwaUserView::class);
    }

    public function incrementViews()
    {
        $this->increment('views');
    }
}
