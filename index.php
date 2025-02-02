<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once 'GameScraper.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Game Scraper Test</title>
</head>
<body>
    <?php
    // Run the test
    GameScraper::runTest();
    ?>
</body>
</html>