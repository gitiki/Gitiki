<?php

namespace Gitiki\Git\Event\Listener;

use Gitiki\Event\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\EventDispatcher\GenericEvent;

class NavigationHistory implements EventSubscriberInterface
{
    public function onNavigation(GenericEvent $event)
    {
        $pageNav = $event->getSubject();

        $pageNav->add('history', 'page', [
            'path' => $pageNav->getPage()->getName(),
            'history' => '',
        ]);
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PAGE_NAVIGATION => ['onNavigation', 2048],
        ];
    }
}
