<?php

namespace MetacriticScraper\Tests;

use PHPUnit\Framework\TestCase;
use MetacriticScraper\GameScraper;

class GameScraperTest extends TestCase
{
    private GameScraper $scraper;

    protected function setUp(): void
    {
        $this->scraper = new GameScraper('league-of-legends-wild-rift');
    }

    public function testGetGameName()
    {
        $name = $this->scraper->getGameName();
        $this->assertNotNull($name);
        $this->assertEquals('League of Legends: Wild Rift', $name);
    }

    public function testGetPlatforms()
    {
        $platforms = $this->scraper->getPlatforms();
        $this->assertIsArray($platforms);
        $this->assertNotEmpty($platforms);
    }

    public function testGetMetascore()
    {
        $score = $this->scraper->getMetascore();
        $this->assertIsInt($score);
        $this->assertGreaterThanOrEqual(0, $score);
        $this->assertLessThanOrEqual(100, $score);
    }

    public function testGetPublisher()
    {
        $publisher = $this->scraper->getPublisher();
        $this->assertNotNull($publisher);
        $this->assertEquals('Riot Games', $publisher);
    }

    public function testGetReleaseDate()
    {
        $date = $this->scraper->getReleaseDate();
        $this->assertNotNull($date);
        $this->assertIsString($date);
    }

    public function testGetSummary()
    {
        $summary = $this->scraper->getSummary();
        $this->assertNotNull($summary);
        $this->assertIsString($summary);
    }

    public function testGetGenres()
    {
        $genres = $this->scraper->getGenres();
        $this->assertIsArray($genres);
        $this->assertNotEmpty($genres);
    }

    public function testGetImage()
    {
        $image = $this->scraper->getImage();
        $this->assertNotNull($image);
        $this->assertIsString($image);
        $this->assertStringContainsString('https://', $image);
    }

    public function testGetRelatedGames()
    {
        $games = $this->scraper->getRelatedGames();
        $this->assertIsArray($games);
        $this->assertNotEmpty($games);
        
        foreach ($games as $game) {
            $this->assertArrayHasKey('name', $game);
            $this->assertArrayHasKey('url', $game);
            $this->assertIsString($game['name']);
            $this->assertIsString($game['url']);
        }
    }
} 