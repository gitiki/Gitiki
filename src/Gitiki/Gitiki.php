<?php

namespace Gitiki;

use Gitiki\Image;

use Silex\Application,
    Silex\Provider;

use Symfony\Component\EventDispatcher\GenericEvent,
    Symfony\Component\HttpFoundation\RedirectResponse;

class Gitiki extends Application
{
    use Application\UrlGeneratorTrait;

    public function __construct()
    {
        parent::__construct();

        $this->register(new Provider\UrlGeneratorServiceProvider());
        $this['url_generator'] = $this->share($this->extend('url_generator', function ($urlGenerator, $app) {
            return new UrlGenerator($urlGenerator);
        }));

        $this->register(new Provider\HttpFragmentServiceProvider());
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
            $dispatcher->addSubscriber(new Event\Listener\WikiLink($this['wiki_dir'], $this['path_resolver']));
            $dispatcher->addSubscriber(new Event\Listener\CodeHighlight($this));

            return $dispatcher;
        }));

        $this['image'] = $this->share(function ($app) {
            if (extension_loaded('gd')) {
                return new Image\GdImage();
            }

            return new Image\NullImage();
        });

        $this['path_resolver'] = $this->share(function ($app) {
            return new PathResolver($app['request_context']);
        });

        $this->error(function ($e, $code) {
            if ($e instanceof Exception\PageRedirectedException) {
                return new RedirectResponse($this->path('page', ['page' => $e->getTarget()]), 301);
            }
        });
    }

    public function getPage($name)
    {
        $event = new GenericEvent(new Page($name));

        $this['dispatcher']->dispatch(Event\Events::PAGE_LOAD, $event);
        $this['dispatcher']->dispatch(Event\Events::PAGE_META, $event);
        $this['dispatcher']->dispatch(Event\Events::PAGE_CONTENT, $event);
        $this['dispatcher']->dispatch(Event\Events::PAGE_TERMINATE, $event);

        return $event->getSubject();
    }
}
