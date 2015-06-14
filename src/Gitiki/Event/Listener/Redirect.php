<?php

namespace Gitiki\Event\Listener;

use Gitiki\Event\Events,
    Gitiki\Exception\PageRedirectedException;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\EventDispatcher\GenericEvent as Event;

class Redirect implements EventSubscriberInterface
{
    public function onMeta(Event $event)
    {
        $page = $event->getSubject();

        if (null !== $redirect = $page->getMeta('redirect')) {
            throw new PageRedirectedException($page->getName(), $redirect);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PAGE_META => ['onMeta', 512],
        ];
    }
}
