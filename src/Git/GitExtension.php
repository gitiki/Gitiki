<?php

namespace Gitiki\Git;

use Gitiki\Extension\BootstrapInterface,
    Gitiki\Extension\WebpackInterface,
    Gitiki\ExtensionInterface,
    Gitiki\Gitiki;

use Gitonomy\Git\Diff\Diff,
    Gitonomy\Git\Repository;

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\Processor,
    Symfony\Component\HttpFoundation\Request;

class GitExtension implements ExtensionInterface, BootstrapInterface, WebpackInterface
{
    public function register(Gitiki $gitiki, array $config)
    {
        $gitiki['git'] = $this->registerConfiguration($gitiki, $config);

        $gitiki['git.repository'] = $gitiki->share(function($gitiki) {
            return new Gitonomy\Repository($gitiki['git']['git_dir'], [
                'debug' => $gitiki['debug'],
                'wiki_dir' => $gitiki['git']['wiki_dir'],
                'git_command' => $gitiki['git']['git_binary'],
                'perl_command' => $gitiki['git']['perl_binary'],
            ]);
        });

        $gitiki['git.controller.diff'] = $gitiki->share(function() use ($gitiki) {
            return new Controller\DiffController();
        });

        $gitiki['git.controller.assets'] = $gitiki->share(function() use ($gitiki) {
            return new Controller\AssetsController();
        });

        $gitiki['translator'] = $gitiki->share($gitiki->extend('translator', function($translator, $gitiki) {
            $translator->addResource('yaml', __DIR__.'/Resources/translations/en.yml', 'en');
            $translator->addResource('yaml', __DIR__.'/Resources/translations/fr.yml', 'fr');

            return $translator;
        }));

        $gitiki['twig.path'] = array_merge($gitiki['twig.path'], [ __DIR__.'/Resources/views' ]);

        $gitiki['dispatcher'] = $gitiki->share($gitiki->extend('dispatcher', function ($dispatcher, $app) {
            $dispatcher->addSubscriber(new Event\Listener\NavigationHistory());

            return $dispatcher;
        }));

        $this->registerRouting($gitiki);
    }

    public function getBootstrap()
    {
        return __DIR__.'/Resources/assets/bootstrap.json';
    }

    public function getWebpackEntries()
    {
        return [
            'git' => __DIR__.'/Resources/assets/css/git.css',
        ];
    }

    public function boot(Gitiki $gitiki)
    {
    }

    protected function registerConfiguration(Gitiki $gitiki, array $config)
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('git');

        $rootNode
            ->children()
                ->scalarNode('git_dir')->defaultValue($gitiki['wiki_path'].'/.git')
                    ->validate()
                    ->always()
                        ->then(function ($v) use ($gitiki) {
                            if ('/' !== $v{0}) {
                                $v = $gitiki['wiki_path'].'/'.$v;
                            }

                            if (is_dir($v.'/.git')) {
                                $v .= '/.git';
                            }

                            return $v;
                        })
                    ->end()
                ->end()
                ->scalarNode('wiki_dir')->defaultValue('')
                    ->validate()
                    ->always()
                        ->then(function ($v) {
                            if ('/' === substr($v, -1)) {
                                $v = substr($v, 0, -1);
                            }

                            return $v;
                        })
                    ->end()
                ->end()
                ->scalarNode('git_binary')->defaultValue('git')->end()
                ->scalarNode('perl_binary')->defaultValue('perl')->end()
            ->end()
        ;

        return (new Processor())->process($treeBuilder->buildTree(), [$config]);
    }

    protected function registerRouting(Gitiki $gitiki)
    {
        $routeCollection = $gitiki['routes'];

        $pageHistory = clone $routeCollection->get('page');
        $pageHistory->run('git.controller.diff:historyAction')
            ->assertGet('history', '')
            ->ifIndex('page', function(Request $request) {
                return [
                    'path' => $request->attributes->get('path'),
                    'history' => '',
                ];
            });
        $routeCollection->addBefore('page', 'git_page_history', $pageHistory);

        $pageDiff = clone $routeCollection->get('page');
        $pageDiff->run('git.controller.diff:diffAction')
            ->assertGet('history', '.+')
            ->ifIndex('page', function(Request $request) {
                return [
                    'path' => $request->attributes->get('path'),
                    'history' => $request->query->get('history'),
                ];
            });
        $routeCollection->addBefore('page', 'git_page_diff', $pageDiff);

        $pageSource = clone $routeCollection->get('page_source');
        $pageSource->run('git.controller.diff:sourceAction')
            ->assertGet('history', '.+');
        $routeCollection->addBefore('page_source', 'git_page_source', $pageSource);
    }
}
