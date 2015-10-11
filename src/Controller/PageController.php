<?php

namespace Gitiki\Controller;

use Gitiki\Exception\PageNotFoundException,
    Gitiki\Gitiki;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\HttpKernelInterface;

class PageController
{
    public function pageAction(Gitiki $gitiki, $path, $_format)
    {
        // the index page cannot be accessed directly by `/index.html` url
        if ('index' === basename($path) && null === $gitiki['request_stack']->getParentRequest()) {
            return $gitiki->redirect($gitiki->path('page', ['path' => '/'.dirname($path).'/index.md']), 301);
        }

        try {
            $page = $gitiki->getPage($path);
        } catch (PageNotFoundException $e) {
            $gitiki->abort(404, sprintf('The page "%s" was not found.', $e->getPage()));
        }

        return $gitiki['twig']->render('page.html.twig', [
            'page' => $page,
        ]);
    }

    public function pageDirectoryAction(Gitiki $gitiki, Request $request, $path)
    {
        return $gitiki->handle(
            Request::create($request->getBaseUrl().'/'.$path.'index.html', 'GET', $request->query->all(), [], [], $request->server->all()),
            HttpKernelInterface::SUB_REQUEST,
            false
        );
    }
}
