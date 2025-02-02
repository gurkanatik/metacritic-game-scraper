# Metacritic Game Scraper

## English

### Overview

This PHP class, `GameScraper`, allows you to scrape game information from Metacritic using `voku/helper` for HTML parsing.

### Features

- Fetches game details such as name, platforms, metascore, publisher, release date, summary, genres, image, and related games.
- Uses `ld+json` structured data for accurate information retrieval.
- Retrieves related games from Metacritic pages.

### Requirements

- PHP 8.0 or higher
- Composer dependencies installed (see `composer.json`)

### Installation

1. Ensure dependencies are installed using Composer:
   ```sh
   composer install
   ```
2. Include the class in your project:
   ```php
   require 'GameScraper.php';
   ```

### Methods

#### `getGameName() : ?string`
Returns the name of the game.

#### `getPlatforms() : ?array`
Returns the platforms the game is available on.

#### `getMetascore() : ?int`
Returns the Metacritic score of the game.

#### `getPublisher() : ?string`
Returns the publisher of the game.

#### `getReleaseDate() : ?string`
Returns the release date of the game.

#### `getSummary() : ?string`
Returns a short description of the game.

#### `getGenres() : ?array`
Returns the genres of the game.

#### `getImage() : ?string`
Returns the URL of the game's cover image.

#### `getRelatedGames() : ?array`
Returns an array of related games with the following structure:
```php
[
    [
        'name' => 'Related Game Name',
        'url' => 'https://www.metacritic.com/game/...'
    ],
    ...
]
```

### Error Handling

The class throws exceptions when the page or JSON data cannot be retrieved.

### Contributing

Contributions are welcome! Feel free to submit a pull request or open an issue.

---

## Türkçe

### Genel Bakış

Bu PHP sınıfı, `GameScraper`, Metacritic'ten oyun bilgilerini kazımak için `voku/helper` kütüphanesini kullanır.

### Özellikler

- Oyun adı, platformlar, metascore, yayıncı, çıkış tarihi, özet, türler, görsel ve ilgili oyunları getirir.
- `ld+json` yapılandırılmış verisini kullanarak doğru bilgi alır.
- Metacritic sayfalarından ilgili oyunları çeker.

### Gereksinimler

- PHP 8.0 veya üzeri
- Composer bağımlılıkları yüklenmiş olmalıdır (`composer.json` dosyasını kontrol edin).

### Kurulum

1. Bağımlılıkların Composer ile yüklendiğinden emin olun:
   ```sh
   composer install
   ```
2. Sınıfı projenize dahil edin:
   ```php
   require 'GameScraper.php';
   ```

### Metodlar

#### `getGameName() : ?string`
Oyunun adını döndürür.

#### `getPlatforms() : ?array`
Oyunun mevcut olduğu platformları döndürür.

#### `getMetascore() : ?int`
Oyunun Metacritic puanını döndürür.

#### `getPublisher() : ?string`
Oyunun yayıncısını döndürür.

#### `getReleaseDate() : ?string`
Oyunun çıkış tarihini döndürür.

#### `getSummary() : ?string`
Oyunun kısa açıklamasını döndürür.

#### `getGenres() : ?array`
Oyunun türlerini döndürür.

#### `getImage() : ?string`
Oyunun kapak resminin URL'sini döndürür.

#### `getRelatedGames() : ?array`
İlgili oyunları aşağıdaki formatta döndürür:
```php
[
    [
        'name' => 'İlgili Oyun Adı',
        'url' => 'https://www.metacritic.com/game/...'
    ],
    ...
]
```

### Hata Yönetimi

Sınıf, sayfa veya JSON verileri alınamazsa istisna (exception) fırlatır.

### Katkıda Bulunma

Katkılar memnuniyetle karşılanır! Bir pull request gönderebilir veya bir hata bildirimi açabilirsiniz.

