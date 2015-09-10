<?php

namespace Gitiki\Controller;

use Gitiki\Gitiki;

class CommonController
{
    private $gitiki;

    public function __construct(Gitiki $gitiki)
    {
        $this->gitiki = $gitiki;
    }

    public function menuAction()
    {
        // the _menu page cannot be accessed directly by `/_menu` url
        if (null === $this->gitiki['request_stack']->getParentRequest()) {
            throw $this->gitiki->abort(404, 'The page "/_menu" cannot be accessed directly.');
        }

        try {
            $page = $this->gitiki->getPage('_menu');
        } catch (PageNotFoundException $e) {
            return '';
        }

        return $this->gitiki['twig']->render('menu.html.twig', [
            'menu' => $page->getMetas(),
        ]);
    }
}
