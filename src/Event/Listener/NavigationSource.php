<?php

namespace Gitiki\Event\Listener;

use Gitiki\Event\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\EventDispatcher\GenericEvent;

class NavigationSource implements EventSubscriberInterface
{
    public function onNavigation(GenericEvent $event)
    {
        $pageNav = $event->getSubject();

        $pageNav->add('file-text', 'page_source', [
            'path' => $pageNav->getPage()->getName()
        ]);
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PAGE_NAVIGATION => ['onNavigation', 1024],
        ];
    }
}
