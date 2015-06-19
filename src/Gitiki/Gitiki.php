<?php

namespace Gitiki;

use Silex\Application,
    Silex\Provider;

use Symfony\Component\EventDispatcher\GenericEvent,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpKernel\Exception\HttpException;

class Gitiki extends Application
{
    use Application\UrlGeneratorTrait;

    public function __construct()
    {
        parent::__construct();

        $this->register(new Provider\UrlGeneratorServiceProvider());
        $this->register(new Provider\TwigServiceProvider(), [
            'twig.path' => __DIR__.'/../views',
        ]);

        $app['twig'] = $this->share($this->extend('twig', function ($twig) {
            $twig->addGlobal('wiki_title', $this['wiki_title']);

            return $twig;
        }));

        $this['dispatcher'] = $this->share($this->extend('dispatcher', function ($dispatcher, $app) {
            $dispatcher->addSubscriber(new Event\Listener\FileLoader($this['wiki_dir']));
            $dispatcher->addSubscriber(new Event\Listener\Metadata());
            $dispatcher->addSubscriber(new Event\Listener\Redirect());
            $dispatcher->addSubscriber(new Event\Listener\Markdown());
            $dispatcher->addSubscriber(new Event\Listener\WikiLink($this['wiki_dir'], $this->path('homepage')));

            return $dispatcher;
        }));

        $this->error(function ($e, $code) {
            if ($e instanceof Exception\PageRedirectedException) {
                return new RedirectResponse($this->path('page', ['page' => $e->getTarget()]), 301);
            }
        });
    }

    public function getPage($name)
    {
        $event = new GenericEvent(new Page($name));

        try {
            $this['dispatcher']->dispatch(Event\Events::PAGE_LOAD, $event);
        } catch (Exception\PageNotFoundException $e) {
            throw new HttpException(404, sprintf('The page "%s" was not found.', $e->getPage()), $e);
        }

        $this['dispatcher']->dispatch(Event\Events::PAGE_META, $event);
        $this['dispatcher']->dispatch(Event\Events::PAGE_CONTENT, $event);
        $this['dispatcher']->dispatch(Event\Events::PAGE_TERMINATE, $event);

        return $event->getSubject();
    }
}
