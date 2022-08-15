<?php

$milliseconds = floor(microtime(true) * 1000);

use App\Api;
use App\Boot;


require_once __DIR__.'/../vendor/autoload.php';

echo (new Api(new Boot()))->getResponse(floor(microtime(true) * 1000) - $milliseconds . " ms");

