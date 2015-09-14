<?php

namespace Gitiki\Event\Listener;

use Gitiki\Event\Events,
    Gitiki\Exception\PageNotFoundException;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\EventDispatcher\GenericEvent as Event;

class FileLoader implements EventSubscriberInterface
{
    protected $wikiDir;

    public function __construct($wikiDir)
    {
        $this->wikiDir = $wikiDir;
    }

    public function onLoad(Event $event)
    {
        $page = $event->getSubject();

        if (!is_file($pagePath = $this->wikiDir.'/'.$page->getName().'.md')) {
            throw new PageNotFoundException($page->getName());
        }

        $page->setContent(file_get_contents($pagePath));
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PAGE_LOAD => ['onLoad', 1024],
        ];
    }
}
