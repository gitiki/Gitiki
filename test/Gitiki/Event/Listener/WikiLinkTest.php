<?php

namespace Gitiki\Test\Event\Listener;

use Gitiki\Event\Listener\WikiLink,
    Gitiki\Page;

use Symfony\Component\EventDispatcher\GenericEvent;

class WikiLinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideContent
     */
    public function testOnMeta($content, $expected, $comment)
    {
        $page = new Page('test');
        $page->setContent($content);

        (new WikiLink(__DIR__.'/fixtures', '/foo.php/'))->onContent(new GenericEvent($page));

        $this->assertSame($expected."\n", $page->getContent(), $comment);
    }

    public function provideContent()
    {
        return [
            ['<p><a href="bar">Bar page</a></p>', '<p><a href="/foo.php/bar">Bar page</a></p>', 'Test link to another wiki page'],
            ['<p><a href="hello">hello</a></p>', '<p><a href="/foo.php/hello" class="new">hello</a></p>', 'Test link to nonexistent wiki page'],
            ['<p><a href="http://example.org/foo">hello</a></p>', '<p><a href="http://example.org/foo">hello</a></p>', 'Test link to other website'],
        ];
    }
}
