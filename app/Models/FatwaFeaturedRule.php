<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FatwaFeaturedRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'hijri_month', 'hijri_day_start', 'hijri_day_end',
        'country_codes', 'category_id', 'madhab_id', 'fatwa_ids',
        'section_label', 'section_sub', 'limit', 'priority', 'is_active',
    ];

    protected $casts = [
        'country_codes' => 'array',
        'fatwa_ids'     => 'array',
        'is_active'     => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function madhab()
    {
        return $this->belongsTo(Madhab::class);
    }

    /** Human-readable Hijri month name */
    public static function hijriMonthName(int $month): string
    {
        return [
            1 => 'Muharram', 2 => 'Safar', 3 => "Rabi' al-Awwal",
            4 => "Rabi' al-Thani", 5 => 'Jumada al-Awwal', 6 => 'Jumada al-Thani',
            7 => 'Rajab', 8 => "Sha'ban", 9 => 'Ramadan',
            10 => 'Shawwal', 11 => "Dhul Qa'dah", 12 => 'Dhul Hijjah',
        ][$month] ?? "Month $month";
    }

    /** Build a human-readable summary of the rule conditions */
    public function conditionSummary(): string
    {
        $parts = [];
        if ($this->hijri_month) {
            $label = self::hijriMonthName($this->hijri_month);
            if ($this->hijri_day_start && $this->hijri_day_end) {
                $label .= " (Day {$this->hijri_day_start}–{$this->hijri_day_end})";
            }
            $parts[] = "📅 $label";
        }
        if (!empty($this->country_codes)) {
            $parts[] = '🌍 ' . implode(', ', $this->country_codes);
        }
        if ($this->category_id) {
            $parts[] = '📂 ' . ($this->category?->name ?? 'Category #' . $this->category_id);
        }
        return $parts ? implode(' + ', $parts) : 'Always active (no conditions)';
    }
}
