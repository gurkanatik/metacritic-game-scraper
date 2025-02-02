<?php

namespace MetacriticScraper;

use voku\helper\HtmlDomParser;
use Exception;

class GameScraper
{
    private ?HtmlDomParser $html;
    private string $baseUrl = 'https://www.metacritic.com/game/';

    public function __construct(string $gameName)
    {
        $formattedGameName = $this->formatGameName($gameName);
        $url = $this->baseUrl . $formattedGameName;
        $this->html = $this->getPage($url);
    }

    private function formatGameName(string $gameName): string
    {
        return strtolower(str_replace(' ', '-', $gameName));
    }

    private function getPage(string $url): ?HtmlDomParser
    {
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ];

        $context = stream_context_create($opts);

        try {
            $html = file_get_contents($url, false, $context);
            return HtmlDomParser::str_get_html($html);
        } catch (Exception $e) {
            throw new Exception("Page could not be loaded: " . $e->getMessage());
        }
    }

    private function getJsonData(): ?array
    {
        if (!$this->html) {
            return null;
        }

        try {
            $jsonContent = '';
            $scripts = $this->html->find('script');
            foreach ($scripts as $script) {
                if ($script->hasAttribute('data-hid') && $script->getAttribute('data-hid') === 'ld+json') {
                    $jsonContent = $script->text;
                    break;
                }
            }

            return json_decode($jsonContent, true);
        } catch (Exception $e) {
            throw new Exception("JSON data could not be parsed: " . $e->getMessage());
        }
    }

    public function getGameName(): ?string
    {
        $jsonData = $this->getJsonData();
        return $jsonData['name'] ?? null;
    }

    public function getPlatforms(): ?array
    {
        $jsonData = $this->getJsonData();
        return $jsonData['gamePlatform'] ?? null;
    }

    public function getMetascore(): ?int
    {
        $jsonData = $this->getJsonData();
        return isset($jsonData['aggregateRating']['ratingValue']) 
            ? (int)$jsonData['aggregateRating']['ratingValue'] 
            : null;
    }

    public function getPublisher(): ?string
    {
        $jsonData = $this->getJsonData();
        return isset($jsonData['publisher'][0]['name']) 
            ? $jsonData['publisher'][0]['name'] 
            : null;
    }

    public function getReleaseDate(): ?string
    {
        $jsonData = $this->getJsonData();
        return $jsonData['datePublished'] ?? null;
    }

    public function getSummary(): ?string
    {
        $jsonData = $this->getJsonData();
        return $jsonData['description'] ?? null;
    }

    public function getGenres(): ?array
    {
        $jsonData = $this->getJsonData();
        if (isset($jsonData['genre'])) {
            return is_array($jsonData['genre']) 
                ? $jsonData['genre'] 
                : [$jsonData['genre']];
        }
        return null;
    }

    public function getImage(): ?string
    {
        $jsonData = $this->getJsonData();
        return $jsonData['image'] ?? null;
    }

    public function getRelatedGames(): ?array
    {
        if (!$this->html) {
            return null;
        }

        try {
            $relatedGames = [];
            $productCards = $this->html->find('div[data-testid="product-card"]');
            
            foreach ($productCards as $card) {
                $container = $card->findOne('.c-globalProductCard_container');
                if ($container && $container->hasAttribute('href')) {
                    $href = $container->getAttribute('href');
                    $gameName = basename($href);
                    $gameName = urldecode($gameName);
                    $gameName = str_replace(['-', '_'], ' ', $gameName);
                    $gameName = ucwords($gameName);
                    
                    $relatedGames[] = [
                        'name' => $gameName,
                        'url' => $href
                    ];
                }
            }

            return !empty($relatedGames) ? $relatedGames : null;
        } catch (Exception $e) {
            throw new Exception("Related games could not be fetched: " . $e->getMessage());
        }
    }
} 