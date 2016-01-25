<?php
define('ROOT', __DIR__);
$loader = require 'backend/vendor/autoload.php';

$app = new \App\App();
$app->run();
