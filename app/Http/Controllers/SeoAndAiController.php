<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\QuestionAndAnswer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class SeoAndAiController extends Controller
{
    /**
     * AI & Search Engine Optimized robots.txt
     */
    public function robotsTxt(): Response
    {
        $sitemapUrl = route('sitemap');

        $content = <<<TXT
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /mufti/
Disallow: /login/
Disallow: /register/
Disallow: /dashboard/
Disallow: /user/
Disallow: /api/
Disallow: /password/

# Sitemaps
Sitemap: {$sitemapUrl}

# LLM & AI Knowledge Standard
# Learn more at https://llmstxt.org/

# OpenAI Crawlers & ChatGPT Live Search
User-agent: GPTBot
Allow: /
Disallow: /admin/
Disallow: /mufti/
Disallow: /user/
Disallow: /api/

User-agent: ChatGPT-User
Allow: /

# Anthropic Claude Crawlers
User-agent: ClaudeBot
Allow: /
Disallow: /admin/
Disallow: /mufti/
Disallow: /user/
Disallow: /api/

User-agent: Claude-Web
Allow: /

# Perplexity AI Search
User-agent: PerplexityBot
Allow: /
Disallow: /admin/
Disallow: /mufti/
Disallow: /user/
Disallow: /api/

# Google AI & Gemini Crawlers
User-agent: Google-Extended
Allow: /

# Apple Intelligence & Siri
User-agent: Applebot
Allow: /

User-agent: Applebot-Extended
Allow: /

# Cohere & Meta AI
User-agent: cohere-ai
Allow: /

User-agent: FacebookBot
Allow: /

TXT;

        return response(trim($content), 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * Dynamic llms.txt standard endpoint for LLMs, AI Search & Crawlers
     */
    public function llmsTxt(): Response
    {
        $siteUrl = url('/');
        $sitemapUrl = route('sitemap');
        
        $categories = Category::where('is_active', true)->whereNull('parent_id')->orderBy('sort_order')->get();
        $featuredFatwas = QuestionAndAnswer::publicArchive()
            ->latest('published_at')
            ->take(12)
            ->get();

        $content = "# Darul Ifta Kauzariyya\n\n";
        $content .= "> Official Islamic Jurisprudence & Fatwa Authority (Shafi & Hanafi Fiqh).\n";
        $content .= "> Publishing authentic, verified Islamic legal rulings in Malayalam, English, Arabic, and Urdu based on the Holy Quran, Prophetic Sunnah, and classical Islamic jurisprudence.\n\n";

        $content .= "## About\n\n";
        $content .= "Darul Ifta Kauzariyya is an authoritative Islamic legal research institution and fatwa council in Kerala, India. It provides research-backed answers to contemporary queries, worship (Ibadah), financial contracts (Muamalat), marriage & family law (Nikah/Talaq), inheritance (Faraid), food & halal compliance, medical ethics, and Islamic theology.\n\n";

        $content .= "## Main Sections & Topic Hierarchy\n\n";
        $content .= "- [Fatwa Archive]({$siteUrl}/fatwa-archive): Searchable repository of public Islamic rulings\n";
        $content .= "- [Ask a Question]({$siteUrl}/ask-question): Submit private religious inquiries to qualified Muftis\n";
        $content .= "- [Scholars & Muftis Directory]({$siteUrl}/team): Certified scholars & verification board\n";
        $content .= "- [Contact Institution]({$siteUrl}/contact): Official institutional contact and inquiries\n\n";

        $content .= "### Primary Jurisprudential Categories\n\n";
        foreach ($categories as $cat) {
            $catUrl = route('fatwa.archive', ['category' => $cat->slug ?: $cat->id]);
            $content .= "- [{$cat->name}]({$catUrl})\n";
        }

        $content .= "\n## Featured & Latest Public Fatwas\n\n";
        foreach ($featuredFatwas as $f) {
            $fTitle = $f->title ?: Str::limit(strip_tags($f->body), 80);
            $fUrl = route('fatwa.show', $f);
            $content .= "- [{$fTitle}]({$fUrl})\n";
        }

        $content .= "\n## Modular XML Sitemaps (Index & Specialized Feeds)\n\n";
        $content .= "- Master Sitemap Index: {$sitemapUrl}\n";
        $content .= "- News / Latest 100 Fatwas: " . route('sitemap.news') . "\n";
        $content .= "- AI Optimized Corpus: " . route('sitemap.ai') . "\n";
        $content .= "- Comprehensive Fatwa Archive: " . route('sitemap.fatwas') . "\n";
        $content .= "- Categories Directory: " . route('sitemap.categories') . "\n";
        $content .= "- Certified Muftis & Scholars: " . route('sitemap.authors') . "\n";
        $content .= "- English Fatwa Directory: " . route('sitemap.en') . "\n";
        $content .= "- Image & Media Sitemap: " . route('sitemap.images') . "\n";
        $content .= "- Core Institutional Pages: " . route('sitemap.pages') . "\n\n";

        $content .= "## Institutional Details\n\n";
        $content .= "- Organization: Darul Ifta Kauzariyya\n";
        $content .= "- Headquarters: Kerala, India\n";
        $content .= "- Primary Jurisprudence: Shafi'i & Hanafi\n";
        $content .= "- Website: {$siteUrl}\n";
        $content .= "- Email: darulifta.kauzariyya@gmail.com\n";

        return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * Root Sitemap Index (/sitemap.xml) linking to all specialized sub-sitemaps
     */
    public function sitemapIndex(): Response
    {
        $latestFatwa = QuestionAndAnswer::publicArchive()->latest('updated_at')->first();
        $latestCategory = Category::latest('updated_at')->first();
        $latestMufti = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))->where('is_active', true)->latest('updated_at')->first();

        $subSitemaps = [
            [
                'loc'     => route('sitemap.news'),
                'lastmod' => ($latestFatwa?->updated_at ?? now())->toAtomString(),
            ],
            [
                'loc'     => route('sitemap.ai'),
                'lastmod' => ($latestFatwa?->updated_at ?? now())->toAtomString(),
            ],
            [
                'loc'     => route('sitemap.fatwas'),
                'lastmod' => ($latestFatwa?->updated_at ?? now())->toAtomString(),
            ],
            [
                'loc'     => route('sitemap.categories'),
                'lastmod' => ($latestCategory?->updated_at ?? now())->toAtomString(),
            ],
            [
                'loc'     => route('sitemap.authors'),
                'lastmod' => ($latestMufti?->updated_at ?? now())->toAtomString(),
            ],
            [
                'loc'     => route('sitemap.en'),
                'lastmod' => ($latestFatwa?->updated_at ?? now())->toAtomString(),
            ],
            [
                'loc'     => route('sitemap.images'),
                'lastmod' => ($latestFatwa?->updated_at ?? now())->toAtomString(),
            ],
            [
                'loc'     => route('sitemap.pages'),
                'lastmod' => now()->toAtomString(),
            ],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($subSitemaps as $sitemap) {
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>" . htmlspecialchars($sitemap['loc']) . "</loc>\n";
            $xml .= "    <lastmod>" . $sitemap['lastmod'] . "</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }

        $xml .= '</sitemapindex>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Sub-Sitemap: Latest 100 Fatwas for Fast Crawling & Google News (/sitemap-news.xml)
     */
    public function sitemapNews(): Response
    {
        $fatwas = QuestionAndAnswer::publicArchive()
            ->with(['category', 'assignedMufti'])
            ->latest('published_at')
            ->take(100)
            ->get();

        $languages = ['en', 'ar', 'ur', 'ml'];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($fatwas as $f) {
            $loc = route('fatwa.show', $f);
            $lastmodStr = ($f->updated_at ?? $f->published_at ?? now())->toAtomString();

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
            
            if ($f->featured_image) {
                $imgUrl = asset('storage/' . $f->featured_image);
                $imgTitle = htmlspecialchars($f->title ?: 'Fatwa #' . $f->question_id);
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>" . htmlspecialchars($imgUrl) . "</image:loc>\n";
                $xml .= "      <image:title>{$imgTitle}</image:title>\n";
                $xml .= "    </image:image>\n";
            }

            foreach ($languages as $lang) {
                $separator = (strpos($loc, '?') !== false) ? '&' : '?';
                $langUrl = $loc . $separator . 'lang=' . $lang;
                $xml .= '    <xhtml:link rel="alternate" hreflang="' . $lang . '" href="' . htmlspecialchars($langUrl) . '"/>' . "\n";
            }
            $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($loc) . '"/>' . "\n";

            $xml .= "    <lastmod>{$lastmodStr}</lastmod>\n";
            $xml .= "    <changefreq>daily</changefreq>\n";
            $xml .= "    <priority>0.95</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Sub-Sitemap: AI Summary Feed for LLMs & AI Search Engines (/sitemap-ai.xml)
     * High priority corpus (Latest 100 + Popular + Transactions + English)
     */
    public function sitemapAi(): Response
    {
        // 1. Latest 100
        $latest = QuestionAndAnswer::publicArchive()->latest('published_at')->take(100)->get();

        // 2. Most Read / Liked (Popular)
        $popular = QuestionAndAnswer::publicArchive()->where(function($q) {
            $q->where('likes', '>', 3)->orWhere('views', '>', 50);
        })->latest('likes')->take(50)->get();

        // 3. English Fatwas
        $english = QuestionAndAnswer::publicArchive()->where('language', 'English')->latest('published_at')->take(50)->get();

        // Merge & Unique by ID
        $allAiFatwas = $latest->merge($popular)->merge($english)->unique('id');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        $languages = ['en', 'ar', 'ur', 'ml'];
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        foreach ($allAiFatwas as $f) {
            $loc = route('fatwa.show', $f);
            $lastmodStr = ($f->updated_at ?? $f->published_at ?? now())->toAtomString();
            
            $isRecent = $f->published_at && $f->published_at->greaterThanOrEqualTo($thirtyDaysAgo);
            $priority = $isRecent ? '0.95' : '0.90';
            $changefreq = $isRecent ? 'daily' : 'weekly';

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
            
            if ($f->featured_image) {
                $imgUrl = asset('storage/' . $f->featured_image);
                $imgTitle = htmlspecialchars($f->title ?: 'Islamic Ruling: ' . Str::limit(strip_tags($f->body), 80));
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>" . htmlspecialchars($imgUrl) . "</image:loc>\n";
                $xml .= "      <image:title>{$imgTitle}</image:title>\n";
                $xml .= "    </image:image>\n";
            }

            foreach ($languages as $lang) {
                $separator = (strpos($loc, '?') !== false) ? '&' : '?';
                $langUrl = $loc . $separator . 'lang=' . $lang;
                $xml .= '    <xhtml:link rel="alternate" hreflang="' . $lang . '" href="' . htmlspecialchars($langUrl) . '"/>' . "\n";
            }
            $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($loc) . '"/>' . "\n";

            $xml .= "    <lastmod>{$lastmodStr}</lastmod>\n";
            $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
            $xml .= "    <priority>{$priority}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Sub-Sitemap: Comprehensive Fatwa Archive with dynamic priority & changefreq (/sitemap-fatwa.xml)
     */
    public function sitemapFatwas(): Response
    {
        $fatwas = QuestionAndAnswer::publicArchive()
            ->latest('published_at')
            ->get(['id', 'question_id', 'title', 'body', 'featured_image', 'likes', 'views', 'updated_at', 'published_at']);

        $languages = ['en', 'ar', 'ur', 'ml'];
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($fatwas as $f) {
            $loc = route('fatwa.show', $f);
            $lastmodStr = ($f->updated_at ?? $f->published_at ?? now())->toAtomString();

            // Calibrated dynamic priority and change frequency calculation
            $isRecent = ($f->published_at && $f->published_at->greaterThanOrEqualTo($thirtyDaysAgo)) ||
                        ($f->updated_at && $f->updated_at->greaterThanOrEqualTo($thirtyDaysAgo));
            $isPopular = ($f->likes >= 5 || $f->views >= 50);

            if ($isRecent) {
                $priority = '0.9';
                $changefreq = 'daily';
            } elseif ($isPopular) {
                $priority = '0.85';
                $changefreq = 'weekly';
            } else {
                $priority = '0.65';
                $changefreq = 'monthly';
            }

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
            
            if ($f->featured_image) {
                $imgUrl = asset('storage/' . $f->featured_image);
                $imgTitle = htmlspecialchars($f->title ?: 'Fatwa #' . $f->question_id);
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>" . htmlspecialchars($imgUrl) . "</image:loc>\n";
                $xml .= "      <image:title>{$imgTitle}</image:title>\n";
                $xml .= "    </image:image>\n";
            }

            foreach ($languages as $lang) {
                $separator = (strpos($loc, '?') !== false) ? '&' : '?';
                $langUrl = $loc . $separator . 'lang=' . $lang;
                $xml .= '    <xhtml:link rel="alternate" hreflang="' . $lang . '" href="' . htmlspecialchars($langUrl) . '"/>' . "\n";
            }
            $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($loc) . '"/>' . "\n";
            
            $xml .= "    <lastmod>{$lastmodStr}</lastmod>\n";
            $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
            $xml .= "    <priority>{$priority}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Sub-Sitemap: English Fatwas (/sitemap-en.xml)
     */
    public function sitemapEn(): Response
    {
        $fatwas = QuestionAndAnswer::publicArchive()
            ->where(function ($q) {
                $q->where('language', 'English')
                  ->orWhere('title', 'REGEXP', '^[a-zA-Z0-9 ]+')
                  ->orWhere('tags', 'LIKE', '%english%');
            })
            ->latest('published_at')
            ->get(['id', 'question_id', 'title', 'body', 'featured_image', 'updated_at', 'published_at']);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($fatwas as $f) {
            $loc = route('fatwa.show', $f);
            $lastmodStr = ($f->updated_at ?? $f->published_at ?? now())->toAtomString();

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
            
            if ($f->featured_image) {
                $imgUrl = asset('storage/' . $f->featured_image);
                $imgTitle = htmlspecialchars($f->title ?: 'English Islamic Ruling');
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>" . htmlspecialchars($imgUrl) . "</image:loc>\n";
                $xml .= "      <image:title>{$imgTitle}</image:title>\n";
                $xml .= "    </image:image>\n";
            }

            $xml .= '    <xhtml:link rel="alternate" hreflang="en" href="' . htmlspecialchars($loc . (strpos($loc, '?') !== false ? '&' : '?') . 'lang=en') . '"/>' . "\n";
            $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($loc) . '"/>' . "\n";

            $xml .= "    <lastmod>{$lastmodStr}</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.85</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Sub-Sitemap: Media & Image Sitemap (/sitemap-image.xml)
     */
    public function sitemapImages(): Response
    {
        $fatwasWithImages = QuestionAndAnswer::publicArchive()
            ->whereNotNull('featured_image')
            ->where('featured_image', '!=', '')
            ->latest('published_at')
            ->get(['id', 'question_id', 'title', 'featured_image', 'updated_at']);

        $scholars = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))
            ->where('is_active', true)
            ->whereNotNull('avatar')
            ->where('avatar', '!=', '')
            ->get(['id', 'name', 'avatar', 'updated_at']);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($fatwasWithImages as $f) {
            $loc = route('fatwa.show', $f);
            $imgUrl = asset('storage/' . $f->featured_image);
            $imgTitle = htmlspecialchars($f->title ?: 'Fatwa Ruling Image #' . $f->question_id);

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
            $xml .= "    <image:image>\n";
            $xml .= "      <image:loc>" . htmlspecialchars($imgUrl) . "</image:loc>\n";
            $xml .= "      <image:title>{$imgTitle}</image:title>\n";
            $xml .= "    </image:image>\n";
            $xml .= "  </url>\n";
        }

        foreach ($scholars as $s) {
            $loc = route('fatwa.mufti.archive', $s->id);
            $imgUrl = asset('storage/' . $s->avatar);
            $imgTitle = htmlspecialchars('Mufti ' . $s->name . ' — Darul Ifta Kauzariyya');

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
            $xml .= "    <image:image>\n";
            $xml .= "      <image:loc>" . htmlspecialchars($imgUrl) . "</image:loc>\n";
            $xml .= "      <image:title>{$imgTitle}</image:title>\n";
            $xml .= "    </image:image>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Sub-Sitemap: All Active Categories (/sitemap-category.xml)
     */
    public function sitemapCategories(): Response
    {
        $categories = Category::where('is_active', true)->get(['id', 'slug', 'name', 'updated_at']);
        $languages = ['en', 'ar', 'ur', 'ml'];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        foreach ($categories as $cat) {
            $loc = route('fatwa.archive', ['category' => $cat->slug ?: $cat->id]);
            $lastmodStr = ($cat->updated_at ?? now())->toAtomString();

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
            
            foreach ($languages as $lang) {
                $separator = (strpos($loc, '?') !== false) ? '&' : '?';
                $langUrl = $loc . $separator . 'lang=' . $lang;
                $xml .= '    <xhtml:link rel="alternate" hreflang="' . $lang . '" href="' . htmlspecialchars($langUrl) . '"/>' . "\n";
            }
            $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($loc) . '"/>' . "\n";

            $xml .= "    <lastmod>{$lastmodStr}</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.80</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Sub-Sitemap: All Scholarly Mufti Portfolios (/sitemap-author.xml)
     */
    public function sitemapAuthors(): Response
    {
        $muftis = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))
            ->where('is_active', true)
            ->get(['id', 'name', 'avatar', 'updated_at']);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($muftis as $m) {
            $loc = route('fatwa.mufti.archive', $m->id);
            $lastmodStr = ($m->updated_at ?? now())->toAtomString();

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
            
            if ($m->avatar) {
                $imgUrl = asset('storage/' . $m->avatar);
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>" . htmlspecialchars($imgUrl) . "</image:loc>\n";
                $xml .= "      <image:title>" . htmlspecialchars($m->name) . "</image:title>\n";
                $xml .= "    </image:image>\n";
            }

            $xml .= "    <lastmod>{$lastmodStr}</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.70</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Sub-Sitemap: Static Institutional & Core Pages (/sitemap-pages.xml)
     */
    public function sitemapPages(): Response
    {
        $pages = [
            ['url' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => route('fatwa.archive'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('team'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => route('contact'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => route('privacy'), 'priority' => '0.4', 'changefreq' => 'yearly'],
            ['url' => route('terms'), 'priority' => '0.4', 'changefreq' => 'yearly'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($pages as $p) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($p['url']) . "</loc>\n";
            $xml .= "    <lastmod>" . now()->toAtomString() . "</lastmod>\n";
            $xml .= "    <changefreq>" . $p['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $p['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * AI-Friendly Clean Markdown Endpoint for specific fatwas (/fatwa-archive/{fatwa}.md)
     */
    public function fatwaMarkdown(QuestionAndAnswer $fatwa): Response
    {
        if ($fatwa->status !== 'published' || !$fatwa->is_public) {
            abort(404, 'Fatwa not accessible or not public.');
        }

        $fatwa->loadMissing(['category', 'madhab', 'assignedMufti', 'references']);

        $title = $fatwa->title ?: 'Fatwa #' . $fatwa->question_id;
        $categoryName = $fatwa->category?->name ?? 'General Jurisprudence';
        $madhabName = $fatwa->madhab?->name ?? "Shafi'i";
        $muftiName = $fatwa->assignedMufti?->name ?? 'Mufti Board, Darul Ifta Kauzariyya';
        $publishedDate = $fatwa->published_at ? $fatwa->published_at->format('F d, Y') : 'N/A';
        $canonicalUrl = route('fatwa.show', $fatwa);

        $md = "# {$title}\n\n";
        $md .= "**Institution:** Darul Ifta Kauzariyya\n";
        $md .= "**Category:** {$categoryName}\n";
        $md .= "**Madhab (School):** {$madhabName}\n";
        $md .= "**Mufti / Jurist:** {$muftiName}\n";
        $md .= "**Published Date:** {$publishedDate}\n";
        $md .= "**Canonical URL:** {$canonicalUrl}\n\n";
        $md .= "---\n\n";

        $md .= "## Question\n\n";
        $md .= trim(strip_tags($fatwa->body)) . "\n\n";

        $md .= "## Answer / Fatwa Ruling\n\n";
        $md .= trim(strip_tags($fatwa->answer)) . "\n\n";

        if ($fatwa->references && $fatwa->references->count() > 0) {
            $md .= "## Scholarly References & Classical Citations\n\n";
            foreach ($fatwa->references as $ref) {
                $refLine = "- **{$ref->book_name}**";
                if ($ref->volume_page) {
                    $refLine .= " (Vol/Page: {$ref->volume_page})";
                }
                if ($ref->arabic_text) {
                    $refLine .= "\n  > " . trim(strip_tags($ref->arabic_text));
                }
                $md .= $refLine . "\n\n";
            }
        }

        $md .= "---\n";
        $md .= "*© " . date('Y') . " Darul Ifta Kauzariyya. Verified and published according to the Islamic legal sources.*";

        return response($md, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'X-Robots-Tag' => 'index, follow',
        ]);
    }
}

