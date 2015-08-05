<?php

namespace Gitiki\Test\Event\Listener;

use Gitiki\Event\Listener\Markdown,
    Gitiki\Page;

use Symfony\Component\EventDispatcher\GenericEvent;

class MarkdownTest extends \PHPUnit_Framework_TestCase
{
    public function testOnContent()
    {
        $page = new Page('test');
        $page->setContent('# Hello World!');

        (new Markdown())->onContent(new GenericEvent($page));

        $this->assertSame('<h1 id="hello-world">Hello World!</h1>', $page->getContent());
    }
}
