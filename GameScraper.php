<?php

require_once 'vendor/autoload.php';

use voku\helper\HtmlDomParser;

class GameScraper
{
    private $html;
    private $baseUrl = 'https://www.metacritic.com/game/';

    public function __construct(string $gameName)
    {
        $formattedGameName = $this->formatGameName($gameName);
        $url = $this->baseUrl . $formattedGameName;

        $this->html = $this->getPage($url);
    }

    private function formatGameName(string $gameName): string
    {
        // Format game name for URL
        return strtolower(str_replace(' ', '-', $gameName));
    }

    private function getPage(string $url): ?HtmlDomParser
    {
        // Define user agent
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

    public function getGameName(): ?string
    {
        // Get game name from JSON data
        $jsonData = $this->getJsonData();
        return $jsonData['name'] ?? null;
    }

    public function getPlatforms(): ?array
    {
        // Get platforms from JSON data
        $jsonData = $this->getJsonData();
        return $jsonData['gamePlatform'] ?? null;
    }

    public function getMetascore(): ?int
    {
        // Get metascore from JSON data
        $jsonData = $this->getJsonData();
        return isset($jsonData['aggregateRating']['ratingValue']) 
            ? (int)$jsonData['aggregateRating']['ratingValue'] 
            : null;
    }

    public function getPublisher(): ?string
    {
        // Get publisher from JSON data
        $jsonData = $this->getJsonData();
        return isset($jsonData['publisher'][0]['name']) 
            ? $jsonData['publisher'][0]['name'] 
            : null;
    }

    public function getReleaseDate(): ?string
    {
        // Get release date from JSON data
        $jsonData = $this->getJsonData();
        return $jsonData['datePublished'] ?? null;
    }

    public function getSummary(): ?string
    {
        // Get description from JSON data
        $jsonData = $this->getJsonData();
        return $jsonData['description'] ?? null;
    }

    public function getGenres(): ?array
    {
        // Get genres from JSON data
        $jsonData = $this->getJsonData();
        // If genre is string, convert it to array
        if (isset($jsonData['genre'])) {
            return is_array($jsonData['genre']) 
                ? $jsonData['genre'] 
                : [$jsonData['genre']];
        }
        return null;
    }

    public function getRelatedGames(): ?array
    {
        // Get related games from product cards
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
                    // URL'den oyun adını çıkar
                    $gameName = basename($href);
                    // URL decode ve tire/alt çizgileri boşluğa çevir
                    $gameName = urldecode($gameName);
                    $gameName = str_replace(['-', '_'], ' ', $gameName);
                    // İlk harfleri büyük yap
                    $gameName = ucwords($gameName);
                    
                    $relatedGames[] = [
                        'name' => $gameName,
                        'url' => $href
                    ];
                }
            }

            return !empty($relatedGames) ? $relatedGames : null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getImage(): ?string
    {
        // Get image from JSON data
        $jsonData = $this->getJsonData();
        return $jsonData['image'] ?? null;
    }

    public function getJsonData(): ?array
    {
        // Get JSON-LD data from script tag
        if (!$this->html) {
            return null;
        }

        try {
            $jsonContent = '';
            // Find the JSON-LD script tag with specific attributes
            $scripts = $this->html->find('script');
            foreach ($scripts as $script) {
                if ($script->hasAttribute('data-hid') && $script->getAttribute('data-hid') == 'ld+json') {
                    $jsonContent = $script->text;
                    continue;
                }
            }

            $data = json_decode($jsonContent, true);
            return $data;

        } catch (Exception $e) {
            echo "Error in getJsonData: " . $e->getMessage();
            return null;
        }
    }

    public function test(): void
    {
        // Test function for scraping game data
        try {
            $scraper = new GameScraper('league-of-legends-wild-rift');
            
            echo "<h2>Game Details:</h2>";
            echo "<p>Name: " . ($scraper->getGameName() ?? 'Not found') . "</p>";
            echo "<p>Release Date: " . ($scraper->getReleaseDate() ?? 'Not found') . "</p>";
            echo "<p>Publisher: " . ($scraper->getPublisher() ?? 'Not found') . "</p>";
            echo "<p>Metascore: " . ($scraper->getMetascore() ?? 'Not found') . "</p>";
            
            // Display platforms
            if ($platforms = $scraper->getPlatforms()) {
                echo "<p>Platforms: " . implode(', ', $platforms) . "</p>";
            }
            
            // Display genres
            if ($genres = $scraper->getGenres()) {
                echo "<p>Genres: " . implode(', ', $genres) . "</p>";
            }
            
            // Display summary
            if ($summary = $scraper->getSummary()) {
                echo "<p>Summary: " . htmlspecialchars($summary) . "</p>";
            }
            
            // Display image
            if ($imageUrl = $scraper->getImage()) {
                echo '<div style="margin: 20px 0;">';
                echo '<img src="' . htmlspecialchars($imageUrl) . '" width="200" height="200" style="object-fit: cover;">';
                echo '</div>';
            }

            // Display related games
            if ($relatedGames = $scraper->getRelatedGames()) {
                echo "<h3>Related Games:</h3>";
                echo "<ul>";
                foreach ($relatedGames as $game) {
                    echo "<li>";
                    echo "<a href='" . htmlspecialchars($game['url']) . "'>";
                    echo htmlspecialchars($game['name']);
                    echo "</a>";
                    echo "</li>";
                }
                echo "</ul>";
            }

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    // Add static test method for easy testing
    public static function runTest(): void
    {
        $instance = new self('league-of-legends-wild-rift');
        $instance->test();
    }
} 