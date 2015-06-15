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

        $doc = new \DOMDocument();
        $doc->loadHTML($page->getContent(), LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD);

        foreach ($doc->getElementsByTagName('a') as $link) {
            $href = $link->getAttribute('href');

            $url = parse_url($href);
            if (isset($url['host'])) {
                continue;
            }

            if (!is_file($this->wikiDir.'/'.$url['path'].'.md')) {
                $link->setAttribute('class', 'new');
            }

            $link->setAttribute('href', $this->baseUri.$href);
        }

        $page->setContent($doc->saveHTML());
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PAGE_CONTENT => ['onContent', 512],
        ];
    }
}
