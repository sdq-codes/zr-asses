<?php

namespace App\Services\ScraperEngine;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ApartmentUrlScrapper
{
    private $timeout = 30;
    private $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    /**
     * Scrape URLs from a given website
     */
    public function scrape(string $url): array
    {
        $startTime = microtime(true);

        try {
            $response = Http::withHeaders([
                'User-Agent' => $this->userAgent,
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ])->timeout($this->timeout)->get($url);

            if (!$response->successful()) {
                throw new \Exception("HTTP {$response->status()} error");
            }

            $html = $response->body();
            $foundUrls = [];

            // Extract all href links
            preg_match_all('/href=["\'](https?:\/\/[^"\']+)["\']/', $html, $absoluteMatches);
            preg_match_all('/href=["\'](\/[^"\']+)["\']/', $html, $relativeMatches);

            // Get base domain
            $parsedUrl = parse_url($url);
            $baseDomain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

            // Process absolute URLs
            foreach ($absoluteMatches[1] as $link) {
                if (strpos($link, $parsedUrl['host']) !== false) {
                    $foundUrls[] = $link;
                }
            }

            // Process relative URLs
            foreach ($relativeMatches[1] as $path) {
                $foundUrls[] = $baseDomain . $path;
            }

            // Remove duplicates
            $foundUrls = array_unique($foundUrls);

            $executionTime = (microtime(true) - $startTime) * 1000;

            return [
                'success' => true,
                'urls' => $foundUrls,
                'execution_time_ms' => (int) $executionTime,
                'count' => count($foundUrls)
            ];

        } catch (\Exception $e) {
            $executionTime = (microtime(true) - $startTime) * 1000;

            return [
                'success' => false,
                'urls' => [],
                'execution_time_ms' => (int) $executionTime,
                'count' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
}
