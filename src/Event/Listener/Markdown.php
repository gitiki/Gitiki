<?php

namespace Gitiki\Event\Listener;

use Gitiki\Event\Events,
    Gitiki\Parser;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\EventDispatcher\GenericEvent as Event;

class Markdown implements EventSubscriberInterface
{
    public function onContent(Event $event)
    {
        (new Parser())->page($event->getSubject());
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PAGE_CONTENT => ['onContent', 1024],
        ];
    }
}
