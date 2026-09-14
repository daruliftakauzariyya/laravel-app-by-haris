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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Bi-directional automatic synchronization of status string and status_id
            if ($model->isDirty('status') && !$model->isDirty('status_id')) {
                $model->status_id = \App\Models\Status::codeToId($model->status);
            } elseif ($model->isDirty('status_id') && !$model->isDirty('status')) {
                $model->status = \App\Models\Status::idToCode($model->status_id);
            } elseif (empty($model->status_id) && !empty($model->status)) {
                $model->status_id = \App\Models\Status::codeToId($model->status);
            } elseif (empty($model->status) && !empty($model->status_id)) {
                $model->status = \App\Models\Status::idToCode($model->status_id);
            }
        });
    }

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

    public function statusRecord()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return Status::getLabel($this->status_id ?? $this->status);
    }

    public function getStatusBadgeAttribute(): string
    {
        return Status::renderBadge($this->status_id ?? $this->status);
    }

    public function scopeStatus($query, $status)
    {
        if (is_array($status)) {
            $ids = array_map(fn($s) => is_numeric($s) ? (int)$s : Status::codeToId($s), $status);
            return $query->whereIn('status_id', $ids);
        }
        $id = is_numeric($status) ? (int)$status : Status::codeToId($status);
        return $query->where('status_id', $id);
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

    /**
     * Parse @{Question-ID} tags in text and convert them to interactive fatwa reference badges.
     */
    public static function parseFatwaReferences(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        // Match @{Question-ID} or @{Q-1234} or @{A0001}
        $pattern = '/@\{([A-Za-z0-9\-_]+)\}/';

        return preg_replace_callback($pattern, function ($matches) {
            $qid = trim($matches[1]);
            $ref = self::where('question_id', $qid)->first();

            if ($ref) {
                $title = strip_tags($ref->title ?: $ref->body);
                $titleShort = \Illuminate\Support\Str::limit($title, 40);
                $url = route('fatwa.show', $ref->id);

                return '<a href="' . $url . '" target="_blank" class="fatwa-tag-pill" title="Referenced Fatwa: ' . e($title) . '">'
                     . '<i class="bi bi-box-arrow-up-right me-1"></i>Ref #' . e($ref->question_id) . ($titleShort ? ' (' . e($titleShort) . ')' : '')
                     . '</a>';
            }

            return '<span class="fatwa-tag-pill not-found">#' . e($qid) . '</span>';
        }, $content);
    }

    /** Accessor for answer with @{Question-ID} tags parsed */
    public function getFormattedAnswerAttribute(): string
    {
        return self::parseFatwaReferences($this->answer ?? $this->draft_answer);
    }

    /** Accessor for question body with @{Question-ID} tags parsed */
    public function getFormattedBodyAttribute(): string
    {
        return self::parseFatwaReferences($this->body);
    }

    /**
     * Accessor for assigned timestamp.
     * Looks up QuestionStatusHistory where question was assigned to this mufti,
     * or defaults to updated_at / created_at if status is 'assigned'.
     */
    public function getAssignedAtAttribute(): ?\Carbon\Carbon
    {
        if (!empty($this->attributes['assigned_at'])) {
            return \Carbon\Carbon::parse($this->attributes['assigned_at']);
        }

        if ($this->relationLoaded('statusHistory') && $this->statusHistory->isNotEmpty()) {
            $history = $this->statusHistory
                ->where('new_assigned_mufti_id', $this->assigned_mufti_id)
                ->sortByDesc('created_at')
                ->first();
            if ($history && $history->created_at) {
                return $history->created_at instanceof \Carbon\Carbon ? $history->created_at : \Carbon\Carbon::parse($history->created_at);
            }

            $assignedStatusHistory = $this->statusHistory
                ->where('new_status', 'assigned')
                ->sortByDesc('created_at')
                ->first();
            if ($assignedStatusHistory && $assignedStatusHistory->created_at) {
                return $assignedStatusHistory->created_at instanceof \Carbon\Carbon ? $assignedStatusHistory->created_at : \Carbon\Carbon::parse($assignedStatusHistory->created_at);
            }
        }

        // Check relationship query if not eagerly loaded
        $historyRecord = $this->statusHistory()
            ->where(function($query) {
                if ($this->assigned_mufti_id) {
                    $query->where('new_assigned_mufti_id', $this->assigned_mufti_id);
                }
                $query->orWhere('new_status', 'assigned');
            })
            ->latest('id')
            ->first();

        if ($historyRecord && $historyRecord->created_at) {
            return $historyRecord->created_at instanceof \Carbon\Carbon ? $historyRecord->created_at : \Carbon\Carbon::parse($historyRecord->created_at);
        }

        // Fallback: If status is assigned or has assigned_mufti_id, fallback to updated_at or created_at
        $fallback = $this->updated_at ?? $this->created_at;
        return $fallback ? ($fallback instanceof \Carbon\Carbon ? $fallback : \Carbon\Carbon::parse($fallback)) : null;
    }

    /**
     * Generate an SEO-friendly URL slug from Question Title or Summary
     * Uses "-" for spaces, removes special symbols, and works seamlessly for all languages (English, Arabic, Urdu, Malayalam).
     */
    public function getSlugAttribute(): string
    {
        // Source text: Title, or first sentence/summary from Question Body
        $source = !empty(trim($this->title ?? ''))
            ? $this->title
            : strip_tags($this->body ?? '');

        // Keep letters (\p{L}), combining marks/vowels (\p{M}), numbers (\p{N}), and spaces
        $cleanText = preg_replace('/[^\p{L}\p{M}\p{N}\s\-]/u', ' ', $source);
        $cleanText = preg_replace('/\s+/u', ' ', trim($cleanText));

        // Form clean hyphenated slug
        $words = explode(' ', $cleanText);
        $words = array_slice($words, 0, 8); // Take top 8 words
        $slug = implode('-', array_filter($words));

        // Limit length cleanly to 90 characters
        if (mb_strlen($slug) > 90) {
            $slug = mb_substr($slug, 0, 90);
        }
        $slug = trim($slug, '-');

        return !empty($slug) ? $slug : 'fatwa-' . $this->id;
    }

    /**
     * Format the route key for automatic URL generation: {id}-{slug}
     * Example: /fatwa-archive/6-ruling-on-fasting-during-travel
     */
    public function getRouteKey()
    {
        return $this->id . '-' . $this->slug;
    }

    /**
     * Resolve route binding from URL identifier
     * Supports:
     * 1. Slug format: '6-ruling-on-fasting' -> ID: 6
     * 2. Integer ID: '6' -> ID: 6
     * 3. Question ID string: 'A0001' or '10219/1849/D=12/1446'
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // 1. Direct Integer ID
        if (is_numeric($value)) {
            return $this->where('id', $value)->first();
        }

        // 2. Combined ID-slug format: '6-ruling-title'
        if (preg_match('/^(\d+)(?:-(.*))?$/', $value, $matches)) {
            $id = $matches[1];
            $fatwa = $this->where('id', $id)->first();
            if ($fatwa) {
                return $fatwa;
            }
        }

        // 3. Question ID string: e.g. A0001 or formatted question ID
        $byQId = $this->where('question_id', $value)->first();
        if ($byQId) {
            return $byQId;
        }

        // 4. URL Decoded check
        $decoded = urldecode($value);
        if ($decoded !== $value) {
            $byDecoded = $this->where('question_id', $decoded)->first();
            if ($byDecoded) {
                return $byDecoded;
            }
        }

        return $this->where('id', $value)->first();
    }
}
