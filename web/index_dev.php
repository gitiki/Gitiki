<?php

error_reporting(-1);
ini_set('display_errors', 1);

require_once __DIR__.'/../vendor/autoload.php';

$app = new Gitiki\Gitiki();

require __DIR__.'/../config/dev.php';
require __DIR__.'/../src/controllers.php';

$app->run();
