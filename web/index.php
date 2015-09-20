<?php

ini_set('display_errors', 0);

require_once __DIR__.'/../vendor/autoload.php';

$app = new Gitiki\Gitiki(__DIR__.'/../wiki/');

require __DIR__.'/../config/prod.php';

$app->run();
