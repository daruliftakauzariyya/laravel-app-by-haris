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

    public static function log($questionId, $event, $label, $icon = 'bi-circle', $color = 'secondary', $notes = null, $userId = null, $fatwaId = null, $eventAt = null)
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
            'event_at'    => $eventAt ?? now(),
        ]);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function questionAndAnswer()
    {
        return $this->belongsTo(QuestionAndAnswer::class, 'question_id');
    }

    public function getEventLabelAttribute($value)
    {
        if ($this->event === 'account_created') {
            $user = $this->actor ?? optional($this->questionAndAnswer)->user;
            $name = $user?->name ?? 'Questioner';
            $country = $user?->country ? " ({$user->country})" : '';
            return "Questioner {$name} Created Account{$country}";
        }

        if ($this->event === 'question_posted') {
            $qid = optional($this->questionAndAnswer)->question_id ?? ('#' . $this->question_id);
            $user = $this->actor ?? optional($this->questionAndAnswer)->user;
            $name = $user?->name ?? 'Questioner';
            return "Question {$qid} Posted by {$name}";
        }

        if ($this->event === 'admin_opened') {
            $adminName = $this->actor?->name ?? 'Admin';
            $qid = optional($this->questionAndAnswer)->question_id ?? ('#' . $this->question_id);
            return "Inquiry {$qid} Opened by Admin {$adminName}";
        }

        if ($this->event === 'accepted') {
            $qid = optional($this->questionAndAnswer)->question_id ?? ('#' . $this->question_id);
            $cat = optional($this->questionAndAnswer?->category)->name ?? 'General';
            return "Question {$qid} Accepted & Categorized under {$cat}";
        }

        if ($this->event === 'mufti_opened' || $value === 'Mufti Opened the Fatwa') {
            $actorName = $this->actor?->name ?? optional($this->questionAndAnswer?->assignedMufti)->name;
            if ($actorName) {
                return "Mufti {$actorName} Opened the Fatwa";
            }
        }

        if ($this->event === 'draft_saved') {
            $qid = optional($this->questionAndAnswer)->question_id ?? ('#' . $this->question_id);
            $actorName = $this->actor?->name ?? optional($this->questionAndAnswer?->assignedMufti)->name ?? 'Scholar';
            return "Fatwa Number {$qid} Draft Saved by Scholar {$actorName}";
        }

        if ($this->event === 'answer_submitted') {
            $qid = optional($this->questionAndAnswer)->question_id ?? ('#' . $this->question_id);
            $actorName = $this->actor?->name ?? optional($this->questionAndAnswer?->assignedMufti)->name ?? 'Scholar';
            return "Answer for Fatwa {$qid} Submitted by Scholar {$actorName}";
        }

        if ($this->event === 'assigned' || $value === 'Assigned to Mufti') {
            $qid = optional($this->questionAndAnswer)->question_id ?? ('#' . $this->question_id);
            $muftiName = null;
            if (!empty($this->attributes['notes']) && preg_match('/Assigned to:\s*(.+)$/i', $this->attributes['notes'], $m)) {
                $muftiName = trim($m[1]);
            }
            if (!$muftiName) {
                $muftiName = optional($this->questionAndAnswer?->assignedMufti)->name ?? 'Mufti';
            }
            return "Assigned {$qid} to Mufti {$muftiName}";
        }

        if ($this->event === 'published') {
            $qid = optional($this->questionAndAnswer)->question_id ?? ('#' . $this->question_id);
            return "Fatwa {$qid} Published to Public Archive";
        }

        if ($this->event === 'liked') {
            $qid = optional($this->questionAndAnswer)->question_id ?? ('#' . $this->question_id);
            return "Someone Liked Fatwa {$qid} and Added to Favorites";
        }

        if ($this->event === 'country_view') {
            $qid = optional($this->questionAndAnswer)->question_id ?? ('#' . $this->question_id);
            return $value ?: "Fatwa {$qid} Read by Public Reader";
        }

        return $value;
    }

    public function getNotesAttribute($value)
    {
        if ($this->event === 'mufti_opened' && $value && str_starts_with($value, 'Mufti opened the fatwa')) {
            $actorName = $this->actor?->name ?? optional($this->questionAndAnswer?->assignedMufti)->name;
            if ($actorName) {
                return preg_replace('/^Mufti opened the fatwa/i', "Mufti {$actorName} opened the fatwa", $value);
            }
        }

        if ($this->event === 'assigned' && $value && preg_match('/^Assigned to:\s*(.+)$/i', $value, $m)) {
            $qid = optional($this->questionAndAnswer)->question_id ?? ('#' . $this->question_id);
            $muftiName = trim($m[1]);
            return "Assigned {$qid} to Mufti {$muftiName}";
        }

        return $value;
    }

    /**
     * Get Complete Chronological / Numbered Lifecycle Timeline for a Question
     */
    public static function getComprehensiveTimeline(QuestionAndAnswer $question)
    {
        $existingLogs = self::where('question_id', $question->id)->with('actor')->orderBy('created_at')->get();
        $timeline = collect($existingLogs);

        // 1. Synthesize Account Creation if not already logged
        if ($question->user && !$timeline->contains('event', 'account_created')) {
            $user = $question->user;
            $timeline->push(new self([
                'question_id' => $question->id,
                'user_id'     => $user->id,
                'event'       => 'account_created',
                'event_label' => "Questioner {$user->name} Created Account" . ($user->country ? " ({$user->country})" : ''),
                'icon'        => 'bi-person-plus',
                'color'       => 'secondary',
                'notes'       => "Questioner account registered @ {$user->created_at->format('Y-m-d H:i:s')} with email {$user->email}" . ($user->country ? ", Country: {$user->country}" : ''),
                'event_at'    => $user->created_at,
                'created_at'  => $user->created_at,
            ]));
        }

        // 2. Synthesize Question Submission if not already logged
        if (!$timeline->contains('event', 'question_posted')) {
            $plainBody = strip_tags($question->body);
            $words = str_word_count($plainBody);
            $chars = mb_strlen($plainBody);
            $sentences = count(array_filter(preg_split('/[.!?\n]+/u', $plainBody), fn($s) => trim($s) !== ''));
            $user = $question->user;

            $timeline->push(new self([
                'question_id' => $question->id,
                'user_id'     => $question->user_id,
                'event'       => 'question_posted',
                'event_label' => "Question {$question->question_id} Posted by " . ($user?->name ?? 'Questioner'),
                'icon'        => 'bi-chat-left-text',
                'color'       => 'info',
                'notes'       => "Inquiry submitted in {$question->language} (Madhab: " . ($question->madhab?->name ?? 'General') . ", Letters: {$chars}, Words: {$words}, Sentences: {$sentences}) @ {$question->created_at->format('Y-m-d H:i:s')}",
                'event_at'    => $question->created_at,
                'created_at'  => $question->created_at,
            ]));
        }

        // 3. Status History Milestones
        foreach ($question->statusHistory as $hist) {
            if ($hist->new_status === 'accepted' && !$timeline->contains('event', 'accepted')) {
                $timeline->push(new self([
                    'question_id' => $question->id,
                    'user_id'     => $hist->user_id,
                    'event'       => 'accepted',
                    'event_label' => "Question {$question->question_id} Accepted & Categorized",
                    'icon'        => 'bi-check-circle',
                    'color'       => 'success',
                    'notes'       => $hist->remarks ?? "Question accepted and categorized by Admin @ {$hist->created_at->format('Y-m-d H:i:s')}",
                    'event_at'    => $hist->created_at,
                    'created_at'  => $hist->created_at,
                ]));
            }

            if ($hist->new_status === 'assigned' && !$timeline->contains(fn($l) => $l->event === 'assigned' && $l->event_at?->equalTo($hist->created_at))) {
                $muftiName = $hist->newMufti?->name ?? $question->assignedMufti?->name ?? 'Mufti';
                $timeline->push(new self([
                    'question_id' => $question->id,
                    'user_id'     => $hist->user_id,
                    'event'       => 'assigned',
                    'event_label' => "Assigned {$question->question_id} to Mufti {$muftiName}",
                    'icon'        => 'bi-person-check',
                    'color'       => 'primary',
                    'notes'       => "Assigned {$question->question_id} to Mufti {$muftiName} @ {$hist->created_at->format('Y-m-d H:i:s')}",
                    'event_at'    => $hist->created_at,
                    'created_at'  => $hist->created_at,
                ]));
            }
        }

        // 4. Synthesize Published Milestone if published and not logged
        if ($question->status === 'published' && $question->published_at && !$timeline->contains('event', 'published')) {
            $timeline->push(new self([
                'question_id' => $question->id,
                'user_id'     => $question->published_by ?? $question->assigned_mufti_id,
                'event'       => 'published',
                'event_label' => "Fatwa {$question->question_id} Published to Public Archive",
                'icon'        => 'bi-globe2',
                'color'       => 'success',
                'notes'       => "Fatwa {$question->question_id} officially approved and published to public portal @ {$question->published_at->format('Y-m-d H:i:s')}",
                'event_at'    => $question->published_at,
                'created_at'  => $question->published_at,
            ]));
        }

        // Sort chronologically
        $sorted = $timeline->sortBy(fn($item) => $item->event_at ?? $item->created_at)->values();

        // Assign sequential number (#1 to #N) and reverse number (#N down to #1)
        $total = $sorted->count();
        $sorted = $sorted->map(function ($item, $index) use ($total) {
            $item->step_number = $index + 1;
            $item->reverse_step_number = $total - $index;
            return $item;
        });

        return $sorted;
    }
}
