<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FatwaActivityLog extends Model
{
    protected $table = 'fatwa_activity_logs';

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

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function fatwa()
    {
        return $this->belongsTo(Fatwa::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Central log-writer helper.
     */
    public static function log(
        int $questionId,
        string $event,
        string $label,
        string $icon = 'bi-circle',
        string $color = 'secondary',
        ?string $notes = null,
        ?int $userId = null,
        ?int $fatwaId = null,
        ?\DateTime $eventAt = null
    ): self {
        return self::create([
            'question_id' => $questionId,
            'fatwa_id'    => $fatwaId,
            'user_id'     => $userId ?? auth()->id(),
            'event'       => $event,
            'event_label' => $label,
            'notes'       => $notes,
            'icon'        => $icon,
            'color'       => $color,
            'event_at'    => $eventAt ?? now(),
        ]);
    }
}
