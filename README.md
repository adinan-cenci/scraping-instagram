# Instagram scraping

This is a PHP library to scrap pictures of profile pages on Instagram. No api token needed.

## Why ?

Some people like to have their Instagram pictures displayed on their website, creating an app and generating a token is too much work for public available information, hence this library.

## What ?

This library will not fetch one's entire gallery, only the few most recent pictures displayed on one's profile page. If you want to display more than 12 pictures at a time you may want to consider caching preview requests.

## How ?

Just pass the user's handle to the constructor and call the ::fetch() method.

```php
use AdinanCenci\ScrapingInstagram\Instagram;

$scraper  = new Instagram('noobmaster');

try {
    $pictures = $scraper->fetch();
} catch (\Exception $e) {
    echo 
    'Error: '.$e->getMessage();
    die();
}
```

It will return an array describing the pictures like so:

```
[
    {
        'caption': 'Lorem ipsum dolor sit amet, consectetur ....',  
        'src': 'https://instagram.ffln3....', 
        'thumbnails': {
            '150x150': 'https://instagram.ffln....', 
            '240x240': 'https://instagram.ffln....', 
            '320x320': 'https://instagram.ffln....', 
            '480x480': 'https://instagram.ffln....', 
            '640x640': 'https://instagram.ffln....'
        }
    }, 
    {
        'caption': 'Sed ut perspiciatis unde omnis iste ....', 
        'src': 'https://instagram.ffln3....', 
        'thumbnails': {
            '150x150': 'https://instagram.ffln....', 
            '240x240': 'https://instagram.ffln....', 
            '320x320': 'https://instagram.ffln....', 
            '480x480': 'https://instagram.ffln....', 
            '640x640': 'https://instagram.ffln....'
        }
    },
    {
        'caption': 'At vero eos et accusamus et iusto odio ....', 
        'src': 'https://instagram.ffln3....', 
        'thumbnails': {
            '150x150': 'https://instagram.ffln....', 
            '240x240': 'https://instagram.ffln....', 
            '320x320': 'https://instagram.ffln....', 
            '480x480': 'https://instagram.ffln....', 
            '640x640': 'https://instagram.ffln....'
        }
    }
]
```

#### ::unique()

A little method to remove duplicated pictures, helpful if you will be merging requests.

```php
$newPictures = $scrapper->fetch();
$pictures    = array_merge($newPictures, $oldPictures);
$pictures    = Instagram::unique($pictures);
```

## Installing

Use composer

```cmd
composer require adinan-cenci/climatempo-api
```

## License

MIT