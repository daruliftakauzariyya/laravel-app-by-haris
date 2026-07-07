<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reference extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_and_answer_id',
        'arabic_text',
        'book_name',
        'language',
        'sort_order',
        'is_draft',
    ];

    protected $casts = [
        'is_draft' => 'boolean',
    ];

    public function questionAndAnswer()
    {
        return $this->belongsTo(QuestionAndAnswer::class, 'question_and_answer_id');
    }
}
