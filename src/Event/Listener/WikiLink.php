<?php

namespace Gitiki\Event\Listener;

use Gitiki\Event\Events,
    Gitiki\PathResolver;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\EventDispatcher\GenericEvent as Event,
    Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WikiLink implements EventSubscriberInterface
{
    protected $wikiDir;

    protected $pathResolver;

    protected $urlGenerator;

    public function __construct($wikiDir, PathResolver $pathResolver, UrlGeneratorInterface $urlGenerator)
    {
        $this->wikiDir = $wikiDir;

        $this->pathResolver = $pathResolver;
        $this->urlGenerator = $urlGenerator;
    }

    public function onContent(Event $event)
    {
        $page = $event->getSubject();

        foreach ($page->getDocument()->getElementsByTagName('a') as $link) {
            $url = parse_url($link->getAttribute('href'));
            if (isset($url['host'])) {
                $link->setAttribute('class', 'external');

                continue;
            } elseif (!isset($url['path'])) { // a internal link can be just a fragment
                continue;
            }

            $href = $this->urlGenerator->generate('page', ['path' => $url['path']]);
            $url['path'] = $this->pathResolver->resolve($url['path']);

            if (!is_file($this->wikiDir.'/'.$url['path'])) {
                $link->setAttribute('class', 'new');
            }

            if (isset($url['fragment'])) {
                $href .= '#'.$url['fragment'];
            }

            $link->setAttribute('href', $href);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PAGE_CONTENT => ['onContent', 512],
        ];
    }
}
