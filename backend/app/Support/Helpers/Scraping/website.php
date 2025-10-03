<?php

namespace App\Support\Helpers\Scraping;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

function fetchWebsiteMetadata(string $url): array
{
    $client = new Client([
        'timeout' => 10,
        'verify' => false, // ignore SSL issues if any
    ]);

    try {
        $response = $client->get($url);
        $html = (string) $response->getBody();

        $crawler = new Crawler($html);

        // Title
        $title = $crawler->filter('title')->count()
            ? $crawler->filter('title')->text()
            : null;

        // Description (meta or og)
        $description = $crawler->filterXPath('//meta[@name="description"]')->count()
            ? $crawler->filterXPath('//meta[@name="description"]')->attr('content')
            : ($crawler->filterXPath('//meta[@property="og:description"]')->count()
                ? $crawler->filterXPath('//meta[@property="og:description"]')->attr('content')
                : null);

        // Image (og:image usually)
        $image = $crawler->filterXPath('//meta[@property="og:image"]')->count()
            ? $crawler->filterXPath('//meta[@property="og:image"]')->attr('content')
            : null;

        return [
            'title'       => $title,
            'description' => $description,
            'image'       => $image,
        ];
    } catch (\Exception $e) {
        return [
            'error' => $e->getMessage(),
        ];
    }
}
