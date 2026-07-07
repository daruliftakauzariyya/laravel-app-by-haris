<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestionStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'question_status_history';

    protected $fillable = [
        'question_and_answer_id',
        'previous_status',
        'new_status',
        'previous_assigned_mufti_id',
        'new_assigned_mufti_id',
        'user_id',
        'remarks',
    ];

    public function questionAndAnswer()
    {
        return $this->belongsTo(QuestionAndAnswer::class, 'question_and_answer_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function previousMufti()
    {
        return $this->belongsTo(User::class, 'previous_assigned_mufti_id');
    }

    public function newMufti()
    {
        return $this->belongsTo(User::class, 'new_assigned_mufti_id');
    }
}
