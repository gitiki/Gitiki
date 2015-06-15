<?php

namespace Gitiki\Event\Listener;

use Gitiki\Event\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\EventDispatcher\GenericEvent as Event,
    Symfony\Component\Yaml\Yaml;

class Metadata implements EventSubscriberInterface
{
    public function onLoad(Event $event)
    {
        $page = $event->getSubject();
        if (!preg_match('/^~{3,}\n(.+)\n~{3,}(?:\n(.*))?$/sU', $page->getContent(), $matches)) {
            return;
        }

        $page->setMetas($matches[1]);
        $page->setContent(
            isset($matches[2]) ? $matches[2] : null
        );
    }

    public function onMeta(Event $event)
    {
        $page = $event->getSubject();

        if (is_string($page->getMetas())) {
            $page->setMetas(Yaml::parse($page->getMetas()));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PAGE_LOAD => ['onLoad', 512],
            Events::PAGE_META => ['onMeta', 1024],
        ];
    }
}
