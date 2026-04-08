<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'user_id',
        'category_id',
        'assigned_mufti_id',
        'title',
        'body',
        'madhab',
        'language',
        'question_type',
        'status',
        'accepted_at',
        'assigned_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    /**
     * Generate a unique IFTA-YYYYMMDD-XXXX question ID.
     */
    public static function generateQuestionId(): string
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count();
        return 'IFTA-' . $date . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedMufti()
    {
        return $this->belongsTo(User::class, 'assigned_mufti_id');
    }

    public function files()
    {
        return $this->hasMany(QuestionFile::class);
    }

    public function muftiAnswer()
    {
        return $this->hasOne(MuftiAnswer::class);
    }

    public function fatwa()
    {
        return $this->hasOne(Fatwa::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(FatwaActivityLog::class);
    }
}
