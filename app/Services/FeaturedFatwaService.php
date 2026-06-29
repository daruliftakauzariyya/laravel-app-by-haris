<?php

namespace App\Services;

use App\Helpers\HijriHelper;
use App\Models\Fatwa;
use App\Models\FatwaFeaturedRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FeaturedFatwaService
{
    /**
     * Resolve the best matching rule and return its fatwas + metadata.
     * Returns an array with keys: 'label', 'sub', 'fatwas', 'rule'
     */
    public function resolve(string $visitorCountry = '', int $hijriMonth = 0, int $hijriDay = 0): array
    {
        // Load all active rules ordered by priority
        $rules = Cache::remember('featured_rules', 3600, function () {
            return FatwaFeaturedRule::where('is_active', true)
                ->with(['category', 'madhab'])
                ->orderByDesc('priority')
                ->get();
        });

        $matchedRule = null;

        foreach ($rules as $rule) {
            if ($this->matches($rule, $visitorCountry, $hijriMonth, $hijriDay)) {
                $matchedRule = $rule;
                break;
            }
        }

        if ($matchedRule) {
            $fatwas = $this->fetchForRule($matchedRule);
            return [
                'label'  => $matchedRule->section_label,
                'sub'    => $matchedRule->section_sub,
                'fatwas' => $fatwas,
                'rule'   => $matchedRule,
            ];
        }

        // No active rule matched — return empty so section is hidden
        return [
            'label'  => '',
            'sub'    => '',
            'fatwas' => collect(),
            'rule'   => null,
        ];
    }

    private function matches(FatwaFeaturedRule $rule, string $country, int $month, int $day): bool
    {
        $conditions = 0;
        $matched    = 0;

        // Condition 1: Hijri month
        if ($rule->hijri_month !== null) {
            $conditions++;
            if ($month === (int) $rule->hijri_month) {
                // Check day range if set
                if ($rule->hijri_day_start && $rule->hijri_day_end) {
                    if ($day >= $rule->hijri_day_start && $day <= $rule->hijri_day_end) {
                        $matched++;
                    }
                } else {
                    $matched++;
                }
            }
        }

        // Condition 2: Country
        if (!empty($rule->country_codes)) {
            $conditions++;
            if ($country && in_array(strtoupper($country), array_map('strtoupper', $rule->country_codes))) {
                $matched++;
            }
        }

        // A rule with no conditions is a catch-all — always matches (lowest priority)
        if ($conditions === 0) return true;

        // All specified conditions must match
        return $matched === $conditions;
    }

    private function fetchForRule(FatwaFeaturedRule $rule): Collection
    {
        $limit = $rule->limit ?: 6;
        $cacheKey = "featured_fatwas_rule_{$rule->id}";

        return Cache::remember($cacheKey, 1800, function () use ($rule, $limit) {
            // If specific fatwas are pinned → show ONLY those, nothing else
            if (!empty($rule->fatwa_ids)) {
                return Fatwa::with(['question.madhab', 'question.category', 'muftiAnswer.mufti'])
                    ->whereIn('id', $rule->fatwa_ids)
                    ->get();
            }

            // Otherwise filter by category / madhab up to limit
            return $this->buildQuery($rule)->take($limit)->get();
        });
    }

    private function buildQuery(FatwaFeaturedRule $rule)
    {
        $query = Fatwa::with(['question.madhab', 'question.category', 'muftiAnswer.mufti'])
            ->orderByDesc('views');

        if ($rule->category_id) {
            $query->whereHas('question', fn($q) => $q->where('category_id', $rule->category_id));
        }

        if ($rule->madhab_id) {
            $query->whereHas('question', fn($q) => $q->where('madhab_id', $rule->madhab_id));
        }

        return $query;
    }
}
