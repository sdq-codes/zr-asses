<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UrlMetadataService
{
    /**
     * Get metadata from a URL
     *
     * @param string $url
     * @return array
     */
    public function getMetadata(string $url): array
    {
        try {
            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new \InvalidArgumentException('Invalid URL provided');
            }

            // Fetch the HTML content
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ])
                ->get($url);

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch URL content');
            }

            $html = $response->body();

            // Parse metadata
            return [
                'name' => $this->extractName($html, $url),
                'favicon' => $this->extractFavicon($html, $url),
                'description' => $this->extractDescription($html),
                'url' => $url,
            ];

        } catch (\Exception $e) {
            Log::error('URL Metadata Extraction Error: ' . $e->getMessage());

            return [
                'name' => $this->extractDomainName($url),
                'favicon' => null,
                'description' => null,
                'url' => $url,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Extract site name from HTML
     */
    private function extractName(string $html, string $url): ?string
    {
        // Try Open Graph title
        if (preg_match('/<meta\s+property=["\']og:site_name["\']\s+content=["\'](.*?)["\']/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        // Try Open Graph title as fallback
        if (preg_match('/<meta\s+property=["\']og:title["\']\s+content=["\'](.*?)["\']/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        // Try regular title tag
        if (preg_match('/<title>(.*?)<\/title>/i', $html, $matches)) {
            return html_entity_decode(trim($matches[1]), ENT_QUOTES, 'UTF-8');
        }

        // Fallback to domain name
        return $this->extractDomainName($url);
    }

    /**
     * Extract favicon from HTML
     */
    private function extractFavicon(string $html, string $url): ?string
    {
        $favicon = null;
        $parsedUrl = parse_url($url);
        $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

        // Try various favicon link tags
        $faviconPatterns = [
            '/<link\s+rel=["\']icon["\']\s+(?:type=["\']\w+\/\w+["\']\s+)?href=["\'](.*?)["\']/i',
            '/<link\s+rel=["\']shortcut icon["\']\s+(?:type=["\']\w+\/\w+["\']\s+)?href=["\'](.*?)["\']/i',
            '/<link\s+rel=["\']apple-touch-icon["\']\s+(?:sizes=["\']\d+x\d+["\']\s+)?href=["\'](.*?)["\']/i',
            '/<link\s+href=["\'](.*?)["\']\s+rel=["\']icon["\']/i',
            '/<link\s+href=["\'](.*?)["\']\s+rel=["\']shortcut icon["\']/i',
        ];

        foreach ($faviconPatterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $favicon = $matches[1];
                break;
            }
        }

        // If no favicon found in HTML, try the default /favicon.ico
        if (!$favicon) {
            $favicon = '/favicon.ico';
        }

        // Convert relative URLs to absolute
        if ($favicon && !filter_var($favicon, FILTER_VALIDATE_URL)) {
            if (strpos($favicon, '//') === 0) {
                // Protocol-relative URL
                $favicon = $parsedUrl['scheme'] . ':' . $favicon;
            } elseif (strpos($favicon, '/') === 0) {
                // Absolute path
                $favicon = $baseUrl . $favicon;
            } else {
                // Relative path
                $favicon = $baseUrl . '/' . $favicon;
            }
        }

        return $favicon;
    }

    /**
     * Extract description from HTML
     */
    private function extractDescription(string $html): ?string
    {
        // Try Open Graph description
        if (preg_match('/<meta\s+property=["\']og:description["\']\s+content=["\'](.*?)["\']/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        // Try Twitter description
        if (preg_match('/<meta\s+name=["\']twitter:description["\']\s+content=["\'](.*?)["\']/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        // Try regular meta description
        if (preg_match('/<meta\s+name=["\']description["\']\s+content=["\'](.*?)["\']/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        return null;
    }

    /**
     * Extract domain name from URL
     */
    private function extractDomainName(string $url): string
    {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? $url;

        // Remove www. prefix
        return preg_replace('/^www\./i', '', $host);
    }
}
