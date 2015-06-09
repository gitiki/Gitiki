<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

$app->get('/', function () use ($app) {
    return $app['twig']->render('page.html.twig', [
        'page' => $app['parser']->parsePage('_index'),
    ]);
})
->bind('homepage')
;

$app->get('/{page}', function ($page) use ($app) {
    return $app['twig']->render('page.html.twig', [
        'page' => $app['parser']->parsePage($page),
    ]);
})
->assert('page', '[\w\d-]+')
->bind('page')
;
