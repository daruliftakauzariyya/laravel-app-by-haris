<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FatwaActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'fatwa_id',
        'user_id',
        'event',
        'event_label',
        'notes',
        'icon',
        'color',
        'event_at',
    ];

    protected $casts = [
        'event_at' => 'datetime',
    ];

    public static function log($questionId, $event, $label, $icon = 'bi-circle', $color = 'secondary', $notes = null, $userId = null, $fatwaId = null)
    {
        return self::create([
            'question_id' => $questionId,
            'fatwa_id'    => $fatwaId,
            'user_id'     => $userId ?? auth()->id(),
            'event'       => $event,
            'event_label' => $label,
            'icon'        => $icon,
            'color'       => $color,
            'notes'       => $notes,
            'event_at'    => now(),
        ]);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
