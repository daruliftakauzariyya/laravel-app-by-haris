<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonLdMiddleware
{
    /**
     * Handle an incoming request and attach Schema.org JSON-LD structured data.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $siteUrl = url('/');
        $logoUrl = asset('assets/logo/Darul-Ifta-Logo.png');

        $globalJsonLd = [
            "@context" => "https://schema.org",
            "@graph" => [
                [
                    "@type" => "Organization",
                    "@id" => "{$siteUrl}/#organization",
                    "name" => "Darul Ifta Kauzariyya",
                    "url" => $siteUrl,
                    "logo" => [
                        "@type" => "ImageObject",
                        "url" => $logoUrl,
                        "caption" => "Darul Ifta Kauzariyya Logo"
                    ],
                    "sameAs" => [
                        "https://facebook.com/daruliftakauzariyya",
                        "https://twitter.com/DarulIftaKz",
                        "https://youtube.com/@daruliftakauzariyya"
                    ],
                    "contactPoint" => [
                        "@type" => "ContactPoint",
                        "contactType" => "customer support",
                        "email" => "darulifta.kauzariyya@gmail.com",
                        "telephone" => "+91 9747650176",
                        "availableLanguage" => ["Malayalam", "English", "Arabic", "Urdu"]
                    ]
                ],
                [
                    "@type" => "WebSite",
                    "@id" => "{$siteUrl}/#website",
                    "url" => $siteUrl,
                    "name" => "Darul Ifta Kauzariyya",
                    "description" => "Authentic Islamic Jurisprudence, Fatwa Archives, and Scholarly Inquiries in Malayalam and English.",
                    "publisher" => [
                        "@id" => "{$siteUrl}/#organization"
                    ],
                    "inLanguage" => ["ml", "en", "ar", "ur"],
                    "potentialAction" => [
                        "@type" => "SearchAction",
                        "target" => [
                            "@type" => "EntryPoint",
                            "urlTemplate" => "{$siteUrl}/fatwa-archive?search={search_term_string}"
                        ],
                        "query-input" => "required name=search_term_string"
                    ]
                ]
            ]
        ];

        view()->share('globalJsonLd', $globalJsonLd);

        return $next($request);
    }
}
