<?php

namespace Gitiki;

use Gitiki\Controller,
    Gitiki\Image;

use Silex\Application,
    Silex\Provider;

use Symfony\Component\EventDispatcher\GenericEvent,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\Translation\Loader\YamlFileLoader;

class Gitiki extends Application
{
    use Application\UrlGeneratorTrait;

    public function __construct()
    {
        parent::__construct();

        $this->register(new Provider\UrlGeneratorServiceProvider());
        $this['url_generator'] = $this->share($this->extend('url_generator', function ($urlGenerator, $app) {
            return new UrlGenerator($app['path_resolver'], $urlGenerator);
        }));

        $this->register(new Provider\TranslationServiceProvider(), array(
            'locale_fallbacks' => array('en'),
        ));
        $this['translator'] = $this->share($this->extend('translator', function($translator, $app) {
            $translator->addLoader('yaml', new YamlFileLoader());

            $translator->addResource('yaml', __DIR__.'/Resources/translations/en.yml', 'en');
            $translator->addResource('yaml', __DIR__.'/Resources/translations/fr.yml', 'fr');

            return $translator;
        }));

        $this->register(new Provider\HttpFragmentServiceProvider());
        $this->register(new Provider\TwigServiceProvider(), [
            'twig.path' => __DIR__.'/Resources/views',
        ]);

        $this['twig'] = $this->share($this->extend('twig', function ($twig, $app) {
            $twig->addExtension(new Twig\CoreExtension($app['translator']));
            $twig->addGlobal('wiki_title', $app['wiki_title']);

            return $twig;
        }));

        $this['dispatcher'] = $this->share($this->extend('dispatcher', function ($dispatcher, $app) {
            $dispatcher->addSubscriber(new Event\Listener\FileLoader($this['wiki_dir']));
            $dispatcher->addSubscriber(new Event\Listener\Metadata());
            $dispatcher->addSubscriber(new Event\Listener\Markdown());
            $dispatcher->addSubscriber(new Event\Listener\WikiLink($this['wiki_dir'], $this['path_resolver'], $this['url_generator']));
            $dispatcher->addSubscriber(new Event\Listener\Image($this['url_generator']));

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

        $this->register(new Provider\ServiceControllerServiceProvider());

        $app = $this;
        $this['controller.assets'] = $this->share(function() use ($app) {
            return new Controller\AssetsController($app);
        });
        $this['controller.common'] = $this->share(function() use ($app) {
            return new Controller\CommonController($app);
        });
        $this['controller.page'] = $this->share(function() use ($app) {
            return new Controller\PageController($app);
        });
        $this['controller.image'] = $this->share(function() use ($app) {
            return new Controller\ImageController($app);
        });

        $this->registerRouting();
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

    protected function registerRouting()
    {
        $this->get('/css/main.css', 'controller.assets:mainCssAction')
            ->bind('asset_css_main');
        $this->get('/bootstrap/css/bootstrap.css', 'controller.assets:bootstrapCssAction')
            ->bind('asset_bootstrap_css');

        $this->get('/_menu', 'controller.common:menuAction');

        $this->get('/{path}', 'controller.page:pageDirectoryAction')
            ->assert('path', '([\w\d-/]+/|)$')
            ->bind('page_dir');

        $this->get('/{path}.{_format}', 'controller.page:pageAction')
            ->assert('path', '[\w\d-/]+')
            ->assert('_format', 'html')
            ->bind('page');

        $this->get('/{path}.{_format}', 'controller.image:imageAction')
            ->assert('path', '[\w\d/]+')
            ->assert('_format', '(jpe?g|png|gif)')
            ->bind('image');
    }
}
