<?php

namespace Gitiki\Event\Listener;

use Gitiki\Event\Events,
    Gitiki\PathResolver;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\EventDispatcher\GenericEvent as Event;

class Image implements EventSubscriberInterface
{
    protected $pathResolver;

    public function __construct(PathResolver $pathResolver)
    {
        $this->pathResolver = $pathResolver;
    }

    public function onContent(Event $event)
    {
        $page = $event->getSubject();

        foreach ($page->getDocument()->getElementsByTagName('img') as $image) {
            $url = parse_url($image->getAttribute('src'));
            if (isset($url['host'])) {
                continue;
            }

            if (isset($url['query'])) {
                parse_str($url['query'], $query);
            }

            $src = $this->pathResolver->getBaseUrl().$this->pathResolver->resolve($url['path']);

            if ('a' !== $image->parentNode->nodeName && (!isset($query['link']) || 'no' !== $query['link'])) {
                $a = $image->parentNode->insertBefore($page->getDocument()->createElement('a'), $image);
                $a->appendChild($image);
                $a->setAttribute('href', $src);
            }

            if (isset($query['link'])) {
                unset($query['link']);
            }

            if (!empty($query)) {
                $src .= '?'.http_build_query($query);
            }

            $image->setAttribute('src', $src);

            unset($query);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PAGE_CONTENT => ['onContent', 256],
        ];
    }
}
