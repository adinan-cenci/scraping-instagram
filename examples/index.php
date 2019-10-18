<?php
use AdinanCenci\ScrapingInstagram\Instagram;
require '../vendor/autoload.php';

//---------------------------------------------

$handle     = 'pewdiepie';
$scraper    = new Instagram($handle);

//---------------------------------------------

try {
    $pictures = $scraper->fetch();
} catch (\Exception $e) {
    echo 
    'Error: '.$e->getMessage();
    die();
}

//---------------------------------------------

foreach ($pictures as $p) {
    echo 
    "<h2>{$p['caption']}</h2>
    <a href=\"{$p['src']}\" target=\"_blank\" title=\"click to enlarge\"> 
        <img src=\"{$p['thumbnails']['150x150']}\" alt=\"{$p['caption']}\" /> 
    </a>";
}