<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestionAndAnswer extends Model
{
    use HasFactory;

    protected $table = 'question_and_answer';

    protected $fillable = [
        'question_id',
        'user_id',
        'category_id',
        'madhab_id',
        'assigned_mufti_id',
        'title',
        'body',
        'language',
        'language_id',
        'status',
        'status_id',
        'answer',
        'draft_answer',
        'published_by',
        'published_at',
        'featured_image',
        'verified_by',
        'checked_approved_by',
        'tags',
        'views',
        'likes',
        'stars',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'tags' => 'array',
        'verified_by' => 'array',
    ];

    /**
     * Generate a unique 5-character question ID (1 alphabet + 4 numbers).
     * Example: A0012
     */
    public static function generateQuestionId(): string
    {
        $latest = self::orderBy('id', 'desc')->first();

        if (!$latest || !preg_match('/^([A-Z])([0-9]{4})$/', $latest->question_id, $matches)) {
            return 'A0001';
        }

        $letter = $matches[1];
        $number = (int)$matches[2];

        $number++;

        if ($number > 9999) {
            $number = 1;
            $letter = chr(ord($letter) + 1);
            if ($letter > 'Z') {
                $letter = 'A';
            }
        }

        return $letter . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function madhab()
    {
        return $this->belongsTo(Madhab::class, 'madhab_id');
    }

    public function assignedMufti()
    {
        return $this->belongsTo(User::class, 'assigned_mufti_id');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'checked_approved_by');
    }

    public function references()
    {
        return $this->hasMany(Reference::class, 'question_and_answer_id')->where('is_draft', false)->orderBy('sort_order');
    }

    public function draftReferences()
    {
        return $this->hasMany(Reference::class, 'question_and_answer_id')->where('is_draft', true)->orderBy('sort_order');
    }

    public function statusHistory()
    {
        return $this->hasMany(QuestionStatusHistory::class, 'question_and_answer_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(FatwaActivityLog::class, 'question_id')->orderBy('created_at', 'desc');
    }

    public function incrementViews()
    {
        $this->increment('views');
    }
}
