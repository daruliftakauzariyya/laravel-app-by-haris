<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Status extends Model
{
    use HasFactory;

    protected $table = 'statuses';

    protected $fillable = [
        'code',
        'name',
        'badge_color',
        'badge_class',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Canonical Status Constants
    public const PENDING    = 1;
    public const ACCEPTED   = 2;
    public const ASSIGNED   = 3;
    public const ANSWERED   = 4;
    public const APPROVED   = 5;
    public const PUBLISHED  = 6;
    public const REJECTED   = 7;
    public const CORRECTION = 8;

    /**
     * Get all active statuses cached globally.
     */
    public static function allCached()
    {
        return Cache::remember('global_statuses_collection', 86400, function () {
            return static::where('is_active', true)->orderBy('sort_order')->get();
        });
    }

    /**
     * Map status ID to Code/Slug.
     */
    public static function idToCode(int|string|null $id): string
    {
        if (!$id) return 'pending';
        $item = static::allCached()->firstWhere('id', (int) $id);
        return $item ? $item->code : 'pending';
    }

    /**
     * Map status Code/Slug to ID.
     */
    public static function codeToId(string|int|null $code): int
    {
        if (is_numeric($code)) return (int) $code;
        if (!$code) return self::PENDING;
        $item = static::allCached()->firstWhere('code', strtolower(trim($code)));
        return $item ? $item->id : self::PENDING;
    }

    /**
     * Get human label for a status (by ID or Code).
     */
    public static function getLabel(int|string|null $status): string
    {
        if (is_numeric($status)) {
            $item = static::allCached()->firstWhere('id', (int) $status);
        } else {
            $item = static::allCached()->firstWhere('code', strtolower(trim((string)$status)));
        }
        return $item ? $item->name : ucfirst((string)$status);
    }

    /**
     * Render HTML badge for a status.
     */
    public static function renderBadge(int|string|null $status): string
    {
        $id = is_numeric($status) ? (int)$status : static::codeToId($status);
        $item = static::allCached()->firstWhere('id', $id);

        if ($item) {
            return sprintf(
                '<span class="%s" style="background-color: %s; color: #fff; padding: 0.35em 0.65em; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">%s</span>',
                e($item->badge_class),
                e($item->badge_color),
                e($item->name)
            );
        }

        return sprintf('<span class="badge bg-secondary">%s</span>', e(ucfirst((string)$status)));
    }

    /**
     * Clear status cache when records change.
     */
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('global_statuses_collection');
        });
        static::deleted(function () {
            Cache::forget('global_statuses_collection');
        });
    }

    public function questions()
    {
        return $this->hasMany(QuestionAndAnswer::class, 'status_id');
    }
}
