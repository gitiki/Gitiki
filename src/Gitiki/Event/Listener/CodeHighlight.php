<?php

namespace Gitiki\Event\Listener;

use Gitiki\Event\Events;

use Silex\Application;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\EventDispatcher\GenericEvent as Event;

class CodeHighLight implements EventSubscriberInterface
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function onContent(Event $event)
    {
        $page = $event->getSubject();

        $noHighlight = [];
        $highlightAdded = false;
        foreach ($page->getDocument()->getElementsByTagName('code') as $code) {
            if (!$code->hasAttribute('class')) {
                $noHighlight[] = $code;

                continue;
            } elseif ($highlightAdded) {
                continue;
            } elseif ('language-nohighlight' === $code->getAttribute('class')) {
                continue;
            }

            $basePath = $this->app['request']->getBasePath().'/highlight';
            $body = $page->getDocument()->getElementsByTagName('body')->item(0);

            // css
            $highlightStyle = $page->getDocument()->createElement('link');
            $highlightStyle->setAttribute('rel', 'stylesheet');
            $highlightStyle->setAttribute('href', $basePath.'/styles/tomorrow.css');
            $body->appendChild($highlightStyle);

            // script
            $body->appendChild(
                $page->getDocument()->createElement('script')
            )->setAttribute('src', $basePath.'/highlight.pack.js');
            $body->appendChild(
                $page->getDocument()->createElement('script', 'hljs.initHighlightingOnLoad();')
            );

            $highlightAdded = true;
        }

        if ($highlightAdded) {
            foreach ($noHighlight as $code) {
                $code->setAttribute('class', 'nohighlight');
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PAGE_CONTENT => ['onContent', 512],
        ];
    }
}
