<?php

namespace Gitiki\Controller;

use Gitiki\Exception\PageNotFoundException,
    Gitiki\Gitiki;

class CommonController
{
    public function menuAction(Gitiki $gitiki)
    {
        // the _menu page cannot be accessed directly by `/_menu` url
        if (null === $gitiki['request_stack']->getParentRequest()) {
            throw $gitiki->abort(404, 'The page "/_menu" cannot be accessed directly.');
        }

        try {
            $page = $gitiki->getPage('_menu');
        } catch (PageNotFoundException $e) {
            return '';
        }

        return $gitiki['twig']->render('menu.html.twig', [
            'menu' => $page->getMetas(),
        ]);
    }
}
