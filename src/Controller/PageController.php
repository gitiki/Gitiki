<?php

namespace Gitiki\Controller;

use Gitiki\Event\Events,
    Gitiki\Exception\PageNotFoundException,
    Gitiki\Gitiki,
    Gitiki\Page,
    Gitiki\PageNav;

use Symfony\Component\EventDispatcher\GenericEvent,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
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

    public function sourceAction(Gitiki $gitiki, Request $request, $path)
    {
        $page = new Page($path);
        $gitiki['dispatcher']->dispatch(Events::PAGE_LOAD, new GenericEvent($page));

        return new Response($page->getContent(), 200, [
            'content-type' => 'text/plain',
        ]);
    }

    public function navigationAction(Gitiki $gitiki, $path)
    {
        $pageNav = new PageNav(new Page('/'.$path));
        $gitiki['dispatcher']->dispatch(Events::PAGE_NAVIGATION, new GenericEvent($pageNav));

        return $gitiki['twig']->render('navigation.html.twig', [
            'nav' => $pageNav,
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
