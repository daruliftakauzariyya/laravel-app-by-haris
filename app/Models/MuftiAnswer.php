<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MuftiAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'mufti_id',
        'summary',
        'full_answer',
        'status',
        'admin_notes',
        'reviewed_by',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function mufti()
    {
        return $this->belongsTo(User::class, 'mufti_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function supportingReferences()
    {
        return $this->hasMany(SupportingReference::class)->orderBy('sort_order');
    }

    public function fatwa()
    {
        return $this->hasOne(Fatwa::class);
    }
}
