<?php

namespace Gitiki;

use Gitiki\Controller,
    Gitiki\Image;

use Silex\Application,
    Silex\Provider;

use Symfony\Component\EventDispatcher\GenericEvent,
    Symfony\Component\Translation\Loader\YamlFileLoader,
    Symfony\Component\Yaml\Yaml;

class Gitiki extends Application
{
    use Application\UrlGeneratorTrait;

    public function __construct($wikiPath)
    {
        if (!is_dir($wikiPath)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a directory', $wikiPath));
        }

        $config = $this->registerConfiguration($wikiPath);

        $extensions = $config['extensions'];
        unset($config['extensions']);

        parent::__construct($config);

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
            'twig.path' => [ __DIR__.'/Resources/views' ],
        ]);

        $this['twig'] = $this->share($this->extend('twig', function ($twig, $app) {
            $twig->addExtension(new Twig\CoreExtension($app['translator']));
            $twig->addGlobal('wiki_name', $app['name']);

            return $twig;
        }));

        $this['dispatcher'] = $this->share($this->extend('dispatcher', function ($dispatcher, $app) {
            $dispatcher->addSubscriber(new Event\Listener\FileLoader($this['wiki_path']));
            $dispatcher->addSubscriber(new Event\Listener\Metadata());
            $dispatcher->addSubscriber(new Event\Listener\Markdown());
            $dispatcher->addSubscriber(new Event\Listener\WikiLink($this['wiki_path'], $this['path_resolver'], $this['url_generator']));
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
        $this->registerExtensions($extensions);
    }

    public function getPage($name)
    {
        $page = new Page($name);

        $this['dispatcher']->dispatch(Event\Events::PAGE_LOAD, new GenericEvent($page));
        $this['dispatcher']->dispatch(Event\Events::PAGE_META, new GenericEvent($page));
        $this['dispatcher']->dispatch(Event\Events::PAGE_CONTENT, new GenericEvent($page));
        $this['dispatcher']->dispatch(Event\Events::PAGE_TERMINATE, new GenericEvent($page));

        return $page;
    }

    protected function registerConfiguration($wikiPath)
    {
        $config = [
            'debug' => false,
            'locale' => 'en',

            'name' => 'Wiki',
            'extensions' => [],
        ];

        if (is_file($wikiPath.'/.gitiki.yml')) {
            $wikiConfig = Yaml::parse(file_get_contents($wikiPath.'/.gitiki.yml'));

            if ($wikiConfig) {
                foreach ($config as $key => $value) {
                    if (isset($wikiConfig[$key])) {
                        $config[$key] = $wikiConfig[$key];
                    }
                }
            }
        }

        $config['wiki_path'] = $wikiPath;

        return $config;
    }

    protected function registerRouting()
    {
        $this->get('/css/main.css', 'controller.assets:mainCssAction')
            ->bind('asset_css_main');
        $this->get('/bootstrap/css/bootstrap.css', 'controller.assets:bootstrapCssAction')
            ->bind('asset_bootstrap_css');

        $this->get('/_menu', 'controller.common:menuAction')
            ->bind('_common_menu');

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

    protected function registerExtensions(array $extensions)
    {
        foreach ($extensions as $class => $config) {
            $this->registerExtension(new $class(), $config);
        }
    }

    protected function registerExtension(ExtensionInterface $extension, array $config = null)
    {
        $this->providers[] = $extension;

        $extension->register($this, $config ?: []);
    }
}
