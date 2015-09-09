<?php

namespace Gitiki\Event\Listener;

use Gitiki\Event\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\EventDispatcher\GenericEvent as Event,
    Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Image implements EventSubscriberInterface
{
    protected $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
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

            $src = $this->urlGenerator->generate('image', ['path' => $url['path']]);

            if ('a' !== $image->parentNode->nodeName && (!isset($query['link']) || 'no' !== $query['link'])) {
                $a = $image->parentNode->insertBefore($page->getDocument()->createElement('a'), $image);
                $a->appendChild($image);
                $a->setAttribute('href', $src.(isset($query['link']) && 'direct' === $query['link'] ? '' : '?details'));
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
