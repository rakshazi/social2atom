# Social networks 2 Atom feed converter

Convert social network feeds to atom feed. No any API key needed.

**Status list**:

* Vk.com: Beta. All ok, except audio (doesn't show them at all)

### Installation

1. Clone this repo
2. Call `php composer.phar update` (get composer before this)
3. Create `config.php` in root folder (near index.php, it's optional):

```php
<?php return array(
    //Cache
    'cache.lifetime' => 86400, //New (uncached) posts will be loaded after 1 hour
    'cache.dir' => '/cache',
    //VK.com
    'vk.token' => 'YOUR VK.com APP TOKEN',
    'vk.count' => 100, //Count of posts to load
);

```

### Usage

* Vk.com: http://example.com/vk/[group_slug], example: http://example.com/vk/cavemanstech

### Some implicit reasons (FAQ)
> Q: Why no API libraries were used?
>
> A: Because API library contains a lot of unnecessary (for this project) things
> (eg: Managing users and groups in VK, but really needed only read access to groups)
> and (owing to first part of answer) a lot of unnecessary dependencies.
