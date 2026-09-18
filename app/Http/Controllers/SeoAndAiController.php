<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\QuestionAndAnswer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $featuredFatwas = QuestionAndAnswer::where('status', 'published')
            ->where('is_public', true)
            ->latest('published_at')
            ->take(8)
            ->get();

        $content = "# Darul Ifta Kauzariyya\n\n";
        $content .= "> Official Islamic Jurisprudence & Fatwa Authority (Shafi & Hanafi Fiqh).\n";
        $content .= "> Publishing authoritative legal rulings (fatwas) in Malayalam, English, Arabic, and Urdu based on the Holy Quran, Prophetic Sunnah, and classical Islamic jurisprudence.\n\n";

        $content .= "## About\n\n";
        $content .= "Darul Ifta Kauzariyya is a premier Islamic scholarly council and institution in Kerala, India. It offers authentic rulings on modern questions, worship, halal transactions, family law (Nikah/Talaq), inheritance, medical ethics, fasting (Ramadan), and Islamic theology.\n\n";

        $content .= "## Main Sections & Topic Hierarchy\n\n";
        $content .= "- [Fatwa Archive]({$siteUrl}/fatwa-archive): Searchable repository of public fatwas\n";
        $content .= "- [Ask a Question]({$siteUrl}/ask-question): Submit private religious inquiries to qualified Muftis\n";
        $content .= "- [Scholars & Muftis Directory]({$siteUrl}/team): Certified scholars & verification board\n";
        $content .= "- [Contact Institution]({$siteUrl}/contact): Official institutional contact and inquiries\n\n";

        $content .= "### Primary Categories\n\n";
        foreach ($categories as $cat) {
            $catUrl = route('fatwa.archive', ['category' => $cat->slug ?: $cat->id]);
            $content .= "- [{$cat->name}]({$catUrl})\n";
        }

        $content .= "\n## Latest Public Fatwas\n\n";
        foreach ($featuredFatwas as $f) {
            $fTitle = $f->title ?: strip_tags($f->body);
            $fUrl = route('fatwa.show', $f);
            $content .= "- [{$fTitle}]({$fUrl})\n";
        }

        $content .= "\n## XML Sitemaps (Index & Sub-Sitemaps)\n\n";
        $content .= "- Main Index: {$sitemapUrl}\n";
        $content .= "- Fatwas Sub-Sitemap: " . route('sitemap.fatwas') . "\n";
        $content .= "- Categories Sub-Sitemap: " . route('sitemap.categories') . "\n";
        $content .= "- Scholars Sub-Sitemap: " . route('sitemap.authors') . "\n";
        $content .= "- Pages Sub-Sitemap: " . route('sitemap.pages') . "\n\n";

        $content .= "## Institutional Details\n\n";
        $content .= "- Organization: Darul Ifta Kauzariyya\n";
        $content .= "- Headquarters: Kerala, India\n";
        $content .= "- Primary Jurisprudence: Shafi'i & Hanafi\n";
        $content .= "- Website: {$siteUrl}\n";
        $content .= "- Email: darulifta.kauzariyya@gmail.com\n";

        return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * Root Sitemap Index (/sitemap.xml) linking to 4 specialized sub-sitemaps
     */
    public function sitemapIndex(): Response
    {
        $latestFatwa = QuestionAndAnswer::where('status', 'published')->where('is_public', true)->latest('updated_at')->first();
        $latestCategory = Category::latest('updated_at')->first();
        $latestMufti = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))->where('is_active', true)->latest('updated_at')->first();

        $subSitemaps = [
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
     * Sub-Sitemap 1: All Public Published Fatwas (/sitemap-fatwa.xml)
     */
    public function sitemapFatwas(): Response
    {
        $fatwas = QuestionAndAnswer::where('status', 'published')
            ->where('is_public', true)
            ->latest('published_at')
            ->get(['id', 'title', 'body', 'updated_at', 'published_at']);

        $languages = ['en', 'ar', 'ur', 'ml'];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        foreach ($fatwas as $f) {
            $loc = route('fatwa.show', $f);
            $lastmodStr = ($f->updated_at ?? $f->published_at ?? now())->toAtomString();

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
            $xml .= "    <priority>0.9</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Sub-Sitemap 2: All Active Categories (/sitemap-category.xml)
     */
    public function sitemapCategories(): Response
    {
        $categories = Category::where('is_active', true)->get(['id', 'slug', 'updated_at']);
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
            $xml .= "    <priority>0.8</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Sub-Sitemap 3: All Scholarly Mufti Portfolios (/sitemap-author.xml)
     */
    public function sitemapAuthors(): Response
    {
        $muftis = User::whereHas('role', fn($q) => $q->where('name', 'mufti'))
            ->where('is_active', true)
            ->get(['id', 'updated_at']);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($muftis as $m) {
            $loc = route('fatwa.mufti.archive', $m->id);
            $lastmodStr = ($m->updated_at ?? now())->toAtomString();

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
            $xml .= "    <lastmod>{$lastmodStr}</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.7</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Sub-Sitemap 4: Static Institutional & Core Pages (/sitemap-pages.xml)
     */
    public function sitemapPages(): Response
    {
        $pages = [
            ['url' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => route('fatwa.archive'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('team'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => route('contact'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => route('privacy'), 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => route('terms'), 'priority' => '0.4', 'changefreq' => 'monthly'],
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
}
