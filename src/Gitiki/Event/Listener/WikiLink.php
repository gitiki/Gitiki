<?php

namespace Gitiki\Event\Listener;

use Gitiki\Event\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\EventDispatcher\GenericEvent as Event;

class WikiLink implements EventSubscriberInterface
{
    protected $wikiDir;

    protected $baseUri;

    public function __construct($wikiDir, $baseUri)
    {
        $this->wikiDir = $wikiDir;

        $this->baseUri = $baseUri;
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
            $href = $link->getAttribute('href');

            $url = parse_url($href);
            if (isset($url['host'])) {
                $link->setAttribute('class', 'external');

                continue;
            }

            if (!is_file($this->wikiDir.'/'.$url['path'].'.md')) {
                $link->setAttribute('class', 'new');
            }

            $link->setAttribute('href', $this->baseUri.$href);
        }

        $nodes = $doc
            ->childNodes->item(2) // html
            ->childNodes->item(0) // body
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
