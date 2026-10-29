<?php

namespace App\Services\Mail;

use DOMDocument;
use DOMNode;

class EmailContentSanitizer
{
    /**
     * Tags allowed in sanitized email body content.
     */
    protected static array $allowedTags = [
        'p', 'br', 'div', 'span', 'strong', 'b', 'em', 'i', 'u', 's', 'strike',
        'ul', 'ol', 'li', 'blockquote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'sub', 'sup', 'a', 'hr', 'table', 'tbody', 'thead', 'tr', 'td', 'th'
    ];

    /**
     * Tags that must be removed entirely along with their children.
     */
    protected static array $dangerousTags = [
        'script', 'style', 'iframe', 'frame', 'object', 'embed', 'form',
        'input', 'button', 'select', 'textarea', 'link', 'meta', 'svg',
        'math', 'base', 'applet'
    ];

    /**
     * Safe CSS style properties permitted in inline styles.
     */
    protected static array $allowedCssProperties = [
        'color', 'background-color', 'font-family', 'font-size', 'font-weight',
        'font-style', 'text-decoration', 'text-align', 'direction', 'line-height',
        'padding', 'padding-left', 'padding-right', 'padding-top', 'padding-bottom',
        'margin', 'margin-left', 'margin-right', 'margin-top', 'margin-bottom',
    ];

    /**
     * Sanitize user-submitted HTML for safe, beautifully formatted email rendering.
     *
     * Handles:
     * - Well-formed rich-text editor HTML (Quill/Froala/TinyMCE)
     * - Plain-text content (converts line breaks to semantic paragraphs)
     * - Legacy HTML-entity-encoded content (e.g. &lt;p&gt;)
     * - Multilingual Malayalam, Arabic, Urdu, English text
     * - Directionality (dir="auto" on blocks)
     * - Compresses excessive empty paragraphs (<p><br></p>)
     * - Strips XSS, scripts, iframes, malicious schemas (javascript:) and handlers (on*)
     *
     * @param string|null $content
     * @return string
     */
    public static function sanitize(?string $content): string
    {
        if ($content === null || trim($content) === '') {
            return '';
        }

        $raw = trim($content);

        // Case C: Detect legacy HTML-entity-encoded content (e.g., &lt;p&gt; or &lt;div&gt;)
        if (preg_match('/&lt;(p|div|br|strong|b|em|i|ul|ol|li|span)\b[^&]*&gt;/i', $raw)) {
            $raw = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        // Case B: If no known HTML tags exist, safely escape and convert newlines to paragraphs
        if (!preg_match('/<\/?(p|div|br|span|strong|b|em|i|u|s|strike|ul|ol|li|blockquote|h[1-6]|table|tbody|thead|tr|td|th|a|hr)\b/i', $raw)) {
            $lines = preg_split('/\r\n|\r|\n/', $raw);
            $paragraphs = [];
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed !== '') {
                    $escaped = htmlspecialchars($trimmed, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    $paragraphs[] = '<p style="margin: 0 0 12px 0; line-height: 1.7;" dir="auto">' . $escaped . '</p>';
                }
            }
            return implode("\n", $paragraphs);
        }

        // Case A, D, E: Parse HTML with DOMDocument
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);

        // Use explicit UTF-8 meta tag to guarantee multi-byte scripts (Malayalam, Arabic, Urdu)
        // are not corrupted into numeric or ISO-8859-1 entities.
        $htmlWrapper = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>' . $raw . '</body></html>';
        $doc->loadHTML($htmlWrapper, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $body = $doc->getElementsByTagName('body')->item(0);
        if (!$body) {
            return htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
        }

        // Recursively clean DOM elements
        self::cleanNode($body, $doc);

        // Render cleaned children
        $cleanedHtml = '';
        foreach ($body->childNodes as $child) {
            $cleanedHtml .= $doc->saveHTML($child);
        }

        // Case D: Compress multiple consecutive empty paragraphs (<p><br></p>) into a single spacer
        $cleanedHtml = preg_replace(
            '/(<p[^>]*>\s*(<br\s*\/?>|&nbsp;|\s)*\s*<\/p>\s*){2,}/i',
            '<p style="margin: 0 0 8px 0;"><br></p>',
            $cleanedHtml
        );

        return trim($cleanedHtml);
    }

    /**
     * Recursively clean a DOMNode, stripping unsafe tags and attributes.
     */
    protected static function cleanNode(DOMNode $node, DOMDocument $doc): void
    {
        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $tagName = strtolower($child->nodeName);

                // If dangerous element, remove completely
                if (in_array($tagName, self::$dangerousTags, true)) {
                    $node->removeChild($child);
                    continue;
                }

                // If element is not in allowed tags, unwrap it (keep its child nodes)
                if (!in_array($tagName, self::$allowedTags, true)) {
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);
                    continue;
                }

                // Clean attributes
                if ($child->hasAttributes()) {
                    $attrsToRemove = [];
                    foreach ($child->attributes as $attr) {
                        $attrName = strtolower($attr->name);
                        $attrVal = trim($attr->value);

                        // Strip all event handlers (onclick, onload, onerror, etc.)
                        if (str_starts_with($attrName, 'on')) {
                            $attrsToRemove[] = $attrName;
                            continue;
                        }

                        // Validate links
                        if ($tagName === 'a' && $attrName === 'href') {
                            $lowerHref = strtolower($attrVal);
                            if (str_starts_with($lowerHref, 'http://') ||
                                str_starts_with($lowerHref, 'https://') ||
                                str_starts_with($lowerHref, 'mailto:') ||
                                str_starts_with($lowerHref, '#')) {
                                continue;
                            } else {
                                $attrsToRemove[] = $attrName;
                                continue;
                            }
                        }

                        // Directionality attribute
                        if ($attrName === 'dir') {
                            $valLower = strtolower($attrVal);
                            if (in_array($valLower, ['ltr', 'rtl', 'auto'], true)) {
                                continue;
                            }
                        }

                        // Safe inline styles
                        if ($attrName === 'style') {
                            $safeStyle = self::cleanStyle($attrVal);
                            if (!empty($safeStyle)) {
                                $child->setAttribute('style', $safeStyle);
                                continue;
                            }
                        }

                        $attrsToRemove[] = $attrName;
                    }

                    foreach ($attrsToRemove as $attrName) {
                        $child->removeAttribute($attrName);
                    }
                }

                // Enhance <a> tags for safe email viewing
                if ($tagName === 'a') {
                    $child->setAttribute('target', '_blank');
                    $child->setAttribute('rel', 'noopener noreferrer');
                    $child->setAttribute('style', 'color: #1a7a4a; text-decoration: underline;');
                }

                // Ensure <p> has email-safe typography and automatic directionality
                if ($tagName === 'p') {
                    $currStyle = $child->getAttribute('style');
                    $marginStyle = 'margin: 0 0 12px 0; line-height: 1.7;';
                    $child->setAttribute('style', $currStyle ? rtrim($currStyle, '; ') . '; ' . $marginStyle : $marginStyle);
                    if (!$child->hasAttribute('dir')) {
                        $child->setAttribute('dir', 'auto');
                    }
                }

                if ($tagName === 'div' && !$child->hasAttribute('dir')) {
                    $child->setAttribute('dir', 'auto');
                }

                self::cleanNode($child, $doc);
            }
        }
    }

    /**
     * Clean inline CSS style string against whitelist of allowed properties.
     */
    protected static function cleanStyle(string $style): string
    {
        if (preg_match('/(expression|behavior|url|javascript|vbscript|<|>)/i', $style)) {
            return '';
        }

        $declarations = explode(';', $style);
        $safeDeclarations = [];

        foreach ($declarations as $declaration) {
            $parts = explode(':', $declaration, 2);
            if (count($parts) === 2) {
                $property = strtolower(trim($parts[0]));
                $value = trim($parts[1]);

                if (in_array($property, self::$allowedCssProperties, true) && !preg_match('/[<>\'"]/', $value)) {
                    $safeDeclarations[] = "{$property}: {$value}";
                }
            }
        }

        return implode('; ', $safeDeclarations);
    }

    /**
     * Convert HTML content to clean, readable plain text for multipart email alternatives.
     *
     * @param string|null $content
     * @return string
     */
    public static function toPlainText(?string $content): string
    {
        if ($content === null || trim($content) === '') {
            return '';
        }

        $text = trim($content);

        // Decode entities if encoded
        if (preg_match('/&lt;(p|div|br|strong|b|em|i)\b[^&]*&gt;/i', $text)) {
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        // Convert block elements to line breaks
        $text = preg_replace('/<\s*br\s*\/?>/i', "\n", $text);
        $text = preg_replace('/<\s*\/\s*(p|div|h[1-6]|li|tr)\s*>/i', "\n\n", $text);
        $text = preg_replace('/<\s*li\s*>/i', "• ", $text);

        // Strip remaining HTML tags
        $text = strip_tags($text);

        // Decode remaining HTML entities (&nbsp;, &amp;, etc.)
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normalize multiple blank lines to a maximum of 2
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text);
    }
}
