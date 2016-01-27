# Social networks 2 Atom feed converter

Convert social network feeds to atom feed. No any API key needed.

**Status list**:

* Vk.com: Beta. All ok, except audio (doesn't show them at all)

### Installation

`composer require rakshazi/social2atom:dev-master`

### Usage

```php
<?php
require 'vendor/autoload.php';
$url = "https://vk.com/apiclub"; //VK group url
$s2a = new \Rakshazi\Social2Atom;
$s2a->setConfig(array(
    'vk.count' => 100, //Maximum count of posts per feed
    'vk.token' => 'YOUR TOKEN' //Needed only for video and audio
));
$atomFeedXML = $s2a->convert($url);

echo $atomFeedXML;
```

### Some implicit reasons (FAQ)
> Q: Why no API libraries were used?
>
> A: Because API library contains a lot of unnecessary (for this project) things
> (eg: Managing users and groups in VK, but really needed only read access to groups)
> and (owing to first part of answer) a lot of unnecessary dependencies.


> Q: How to get vk.com token?
>
> A: Read all info here: https://vk.com/dev/auth_sites
