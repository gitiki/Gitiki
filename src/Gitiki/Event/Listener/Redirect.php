<?php

namespace Gitiki\Event\Listener;

use Gitiki\Event\Events,
    Gitiki\Exception\PageRedirectedException,
    Gitiki\PathResolver;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\EventDispatcher\GenericEvent as Event;

class Redirect implements EventSubscriberInterface
{
    public function __construct(PathResolver $pathResolver)
    {
        $this->pathResolver = $pathResolver;
    }

    public function onMeta(Event $event)
    {
        $page = $event->getSubject();

        if (null !== $redirect = $page->getMeta('redirect')) {
            throw new PageRedirectedException($page->getName(), $this->pathResolver->resolve($redirect));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PAGE_META => ['onMeta', 512],
        ];
    }
}
