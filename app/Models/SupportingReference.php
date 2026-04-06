<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupportingReference extends Model
{
    use HasFactory;

    protected $fillable = ['mufti_answer_id', 'arabic_text', 'book_name', 'language', 'sort_order'];

    public function muftiAnswer()
    {
        return $this->belongsTo(MuftiAnswer::class);
    }
}
