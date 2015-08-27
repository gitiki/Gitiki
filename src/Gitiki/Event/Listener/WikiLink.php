<?php

namespace Gitiki\Event\Listener;

use Gitiki\Event\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\EventDispatcher\GenericEvent as Event,
    Symfony\Component\Routing\RequestContext;

class WikiLink implements EventSubscriberInterface
{
    protected $wikiDir;

    protected $context;

    public function __construct($wikiDir, RequestContext $context)
    {
        $this->wikiDir = $wikiDir;

        $this->context = $context;
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

            $link->setAttribute('href', $this->context->getBaseUrl().'/'.$href);
        }

        foreach ($doc->getElementsByTagName('img') as $image) {
            $src = $image->getAttribute('src');

            $url = parse_url($src);
            if (isset($url['host'])) {
                continue;
            }

            $image->setAttribute('src', $this->context->getBaseUrl().'/'.$src);
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
