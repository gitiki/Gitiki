<?php

namespace Gitiki\Event\Listener;

use Gitiki\Event\Events,
    Gitiki\PathResolver;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\EventDispatcher\GenericEvent as Event,
    Symfony\Component\Routing\RequestContext;

class WikiLink implements EventSubscriberInterface
{
    protected $wikiDir;

    protected $pathResolver;

    public function __construct($wikiDir, PathResolver $pathResolver)
    {
        $this->wikiDir = $wikiDir;

        $this->pathResolver = $pathResolver;
    }

    public function onContent(Event $event)
    {
        $page = $event->getSubject();

        // php 5.4 empty function needs to use a variable
        $content = $page->getContent();
        if (empty($content)) {
            return;
        }

        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml encoding="UTF-8">'.$page->getContent());

        foreach ($doc->getElementsByTagName('a') as $link) {
            $url = parse_url($link->getAttribute('href'));
            if (isset($url['host'])) {
                $link->setAttribute('class', 'external');

                continue;
            }

            $url['path'] = $this->pathResolver->resolve($url['path']);

            if (!is_file($this->wikiDir.'/'.$url['path'].'.md')) {
                $link->setAttribute('class', 'new');
            }

            $href = $this->pathResolver->getBaseUrl().$url['path'];
            if (isset($url['fragment'])) {
                $href .= '#'.$url['fragment'];
            }

            $link->setAttribute('href', $href);
        }

        foreach ($doc->getElementsByTagName('img') as $image) {
            $url = parse_url($image->getAttribute('src'));
            if (isset($url['host'])) {
                continue;
            }

            if (isset($url['query'])) {
                parse_str($url['query'], $query);
            }

            $src = $this->pathResolver->getBaseUrl().$this->pathResolver->resolve($url['path']);

            if ('a' !== $image->parentNode->nodeName && !isset($query['nolink'])) {
                $a = $image->parentNode->insertBefore($doc->createElement('a'), $image);
                $a->appendChild($image);
                $a->setAttribute('href', $src);
            }

            if (isset($query['nolink'])) {
                unset($query['nolink']);
            }

            if (!empty($query)) {
                $src .= '?'.http_build_query($query);
            }

            $image->setAttribute('src', $src);

            unset($query);
        }

        $nodes = $doc
            ->childNodes->item(2) // html
            ->firstChild // body
            ->childNodes;
        $content = '';
        foreach ($nodes as $node) {
            $content .= $doc->saveHTML($node);
        }

        $page->setContent($content);
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PAGE_CONTENT => ['onContent', 512],
        ];
    }
}
