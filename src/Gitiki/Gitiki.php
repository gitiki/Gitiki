<?php

namespace Gitiki;

use Silex\Application,
    Silex\Provider;

use Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Response;

class Gitiki extends Application
{
    use Application\UrlGeneratorTrait;

    public function __construct()
    {
        parent::__construct();

        $this->register(new Provider\UrlGeneratorServiceProvider());

        $this['parser'] = $this->share(function () {
            return new Parser($this['wiki_dir'], $this->path('homepage'));
        });

        $this->error(function ($e, $code) {
            if ($e instanceof Exception\PageNotFoundException) {
                if (null !== $redirect = $e->getMeta('redirect')) {
                    return new RedirectResponse($this->path('page', ['page' => $redirect]), 301);
                }

                return new Response(sprintf('The page "%s" not found.', $e->getPage()), 404);
            }
        });
    }
}
