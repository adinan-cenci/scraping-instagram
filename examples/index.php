<?php
use AdinanCenci\ScrapingInstagram\Instagram;

require '../vendor/autoload.php';

$inst = new Instagram('pewdiepie');

$pictures = $inst->get();

foreach ($pictures as $p) {
    echo 
    '<img src="'.$p.'" />';
}