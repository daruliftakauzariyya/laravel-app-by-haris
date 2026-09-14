<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestionFile extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'original_name', 'file_path', 'file_type', 'file_size'];

    public function question()
    {
        return $this->belongsTo(QuestionAndAnswer::class, 'question_id');
    }
}
