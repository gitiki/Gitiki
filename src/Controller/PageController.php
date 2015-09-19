<?php

namespace Gitiki\Controller;

use Gitiki\Exception\PageNotFoundException,
    Gitiki\Gitiki;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\HttpKernelInterface;

class PageController
{
    private $gitiki;

    public function __construct(Gitiki $gitiki)
    {
        $this->gitiki = $gitiki;
    }

    public function pageAction($path, $_format)
    {
        // the index page cannot be accessed directly by `/index.html` url
        if ('index' === basename($path) && null === $this->gitiki['request_stack']->getParentRequest()) {
            return $this->gitiki->redirect($this->gitiki->path('page', ['path' => '/'.dirname($path).'/index.md']), 301);
        }

        try {
            $page = $this->gitiki->getPage($path);
        } catch (PageNotFoundException $e) {
            $this->gitiki->abort(404, sprintf('The page "%s" was not found.', $e->getPage()));
        }

        return $this->gitiki['twig']->render('page.html.twig', [
            'page' => $page,
        ]);
    }

    public function pageDirectoryAction(Request $request, $path)
    {
        return $this->gitiki->handle(
            Request::create($request->getBaseUrl().'/'.$path.'index.html', 'GET', [], [], [], $request->server->all()),
            HttpKernelInterface::SUB_REQUEST,
            false
        );
    }
}
