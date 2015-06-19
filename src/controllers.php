<?php

use Gitiki\Exception\PageNotFoundException;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\HttpException;

$app->get('/', function () use ($app) {
    try {
        $page = $app->getPage('_index');
    } catch (PageNotFoundException $e) {
        throw new HttpException(404, sprintf('The page "%s" was not found.', $e->getPage()), $e);
    }

    return $app['twig']->render('page.html.twig', [
        'page' => $page,
    ]);
})
->bind('homepage')
;

$app->get('/{page}', function ($page) use ($app) {
    try {
        $page = $app->getPage($page);
    } catch (PageNotFoundException $e) {
        throw new HttpException(404, sprintf('The page "%s" was not found.', $e->getPage()), $e);
    }

    return $app['twig']->render('page.html.twig', [
        'page' => $page,
    ]);
})
->assert('page', '[\w\d-]+')
->bind('page')
;
