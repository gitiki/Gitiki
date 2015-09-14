<?php

namespace Gitiki\Test\Event\Listener;

use Gitiki\Event\Listener\FileLoader,
    Gitiki\Page;

use Symfony\Component\EventDispatcher\GenericEvent;

class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testOnLoad()
    {
        $page = new Page('bar');

        (new FileLoader(__DIR__.'/fixtures'))->onLoad(new GenericEvent($page));

        $this->assertSame(file_get_contents(__DIR__.'/fixtures/bar.md'), $page->getContent());
    }

    public function testOnLoadWithNonexistentPage()
    {
        $page = new Page('nonexistent');

        $this->setExpectedException('Gitiki\\Exception\\PageNotFoundException');
        (new FileLoader(__DIR__.'/fixtures'))->onLoad(new GenericEvent($page));
    }
}
