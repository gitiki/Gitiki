<?php

use Gitiki\Exception\PageNotFoundException;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\HttpException,
    Symfony\Component\HttpKernel\HttpKernelInterface;

$app->get('/', function (Request $request) use ($app) {
    return $app->handle(
        Request::create($request->getBaseUrl().'/_index', 'GET', [], [], [], $request->server->all()),
        HttpKernelInterface::SUB_REQUEST,
        false
    );
})
->bind('homepage')
;

$app->get('/_menu', function () use ($app) {
    try {
        $page = $app->getPage('_menu');
    } catch (PageNotFoundException $e) {
        return '';
    }

    return $app['twig']->render('menu.html.twig', [
        'menu' => $page->getMetas(),
    ]);
});

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
