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

        // php 5.4 empty function needs to use a variable
        $content = $page->getContent();
        if (empty($content)) {
            return;
        }

        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml encoding="UTF-8">'.$page->getContent());

        $noHighlight = [];
        $highlightAdded = false;
        foreach ($doc->getElementsByTagName('code') as $code) {
            if (!$code->hasAttribute('class')) {
                $noHighlight[] = $code;

                continue;
            } elseif ($highlightAdded) {
                continue;
            } elseif ('language-nohighlight' === $code->getAttribute('class')) {
                continue;
            }

            $basePath = $this->app['request']->getBasePath().'/highlight';
            $body = $doc->getElementsByTagName('body')->item(0);

            // css
            $highlightStyle = $doc->createElement('link');
            $highlightStyle->setAttribute('rel', 'stylesheet');
            $highlightStyle->setAttribute('href', $basePath.'/styles/tomorrow.css');
            $body->appendChild($highlightStyle);

            // script
            $body->appendChild(
                $doc->createElement('script')
            )->setAttribute('src', $basePath.'/highlight.pack.js');
            $body->appendChild(
                $doc->createElement('script', 'hljs.initHighlightingOnLoad();')
            );

            $highlightAdded = true;
        }

        if ($highlightAdded) {
            foreach ($noHighlight as $code) {
                $code->setAttribute('class', 'nohighlight');
            }
        }

        $nodes = $doc
            ->childNodes->item(2) // html
            ->firstChild // body
            ->childNodes;
        $content = '';
        foreach ($nodes as $node) {
            $content .= $doc->saveHTML($node);
        }

        $page->setContent($content);
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PAGE_CONTENT => ['onContent', 512],
        ];
    }
}
