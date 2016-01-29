# Social networks 2 Atom feed converter

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/12dbb7ee-77e8-4f55-8a71-8a9da4c0c576/big.png)](https://insight.sensiolabs.com/projects/12dbb7ee-77e8-4f55-8a71-8a9da4c0c576)

Convert social network feeds to atom feed.

**Status list**:

* Vk.com: support post, audio, video, photo, link attachments
* Facebook.com: support post (only text for this moment) from pages

### Installation

`composer require rakshazi/social2atom:dev-master`

### Usage

```php
<?php
require 'vendor/autoload.php';
$url = "https://vk.com/apiclub"; //VK group url
$s2a = new \Rakshazi\Social2Atom;
$s2a->setConfig(array(
    //For VK.com (Only if you really need it)
    'vk.count' => 100, //Maximum count of posts per feed
    'vk.token' => 'YOUR TOKEN', //Needed only for video and audio
    //For Facebook.com (Only if you really need it)
    'facebook.count' => 100,
    'facebook.token' => 'YOUR TOKEN', //Needed for all, use App Token
));
$atomFeedXML = $s2a->convert($url);

echo $atomFeedXML;
```

### Some implicit reasons (FAQ)
> Q: How to get vk.com token?
>
> A: Read all info here: https://vk.com/dev/auth_sites, you need following scopes:
> `video,offline`

> Q: How to get facebook.com token?
>
> A: Create app (for websites) and get it token (App token) here: https://developers.facebook.com/tools/accesstoken/

> Q: Why no API libraries were used?
>
> A: Because API library contains a lot of unnecessary (for this project) things
> (eg: Managing users and groups in VK, but really needed only read access to groups)
> and (owing to first part of answer) a lot of unnecessary dependencies.
