<?php

use Gitiki\Exception\InvalidSizeException,
    Gitiki\Exception\PageNotFoundException,
    Gitiki\Image;

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
->assert('page', '[\w\d-/]+')
->bind('page')
;

$app->get('/{path}', function (Request $request, $path) use ($app) {
    $image = new Image($app['wiki_dir'], $path);
    if (false === $image->isFile() || false === $image->isReadable()) {
        $app->abort(404, sprintf('The image "%s" was not found.', $image->getRelativePath()));
    }

    if ($request->query->has('details')) {
        return $app['twig']->render('image.html.twig', [
            'page' => $image,
        ]);
    }

    if (null !== $size = $request->query->get('size')) {
        try {
            return $app->sendFile($app['image']->resize($image, $size));
        } catch (InvalidSizeException $e) {
        }
    }

    return $app->sendFile($image);
})
->assert('path', '[\w\d/]+\.(jpe?g|png|gif)')
->bind('image')
;
