<?php

namespace Gitiki\Test\Event\Listener;

use Gitiki\Event\Listener\WikiLink,
    Gitiki\Page,
    Gitiki\PathResolver;

use Symfony\Component\EventDispatcher\GenericEvent,
    Symfony\Component\Routing\RequestContext;

class WikiLinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideContent
     */
    public function testOnMeta($content, $expected, $comment)
    {
        $page = new Page('test');
        $page->setContent($content);

        (new WikiLink(
            __DIR__.'/fixtures',
            new PathResolver(new RequestContext('/foo.php'))
        ))->onContent(new GenericEvent($page));

        $this->assertSame($expected, $page->getContent(), $comment);
    }

    public function provideContent()
    {
        return [
            ['', '', 'Test with empty content'],
            ['<p><a href="bar">Bar page</a></p>', '<p><a href="/foo.php/bar">Bar page</a></p>', 'Test link to another wiki page'],
            ['<p><a href="hello">hello</a></p>', '<p><a href="/foo.php/hello" class="new">hello</a></p>', 'Test link to nonexistent wiki page'],
            ['<p><a href="foo#description">foo</a></p>', '<p><a href="/foo.php/foo#description" class="new">foo</a></p>', 'Test link with a fragment'],
            ['<p><a href="http://example.org/foo">hello</a></p>', '<p><a href="http://example.org/foo" class="external">hello</a></p>', 'Test link to other website'],
            ['<p><a href="brian">Où est Brian?</a></p>', '<p><a href="/foo.php/brian" class="new">Où est Brian?</a></p>', 'Test with utf-8 content'],
        ];
    }
}
