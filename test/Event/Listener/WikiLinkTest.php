<?php

namespace Gitiki\Event\Listener;

use Gitiki\Event\Listener\WikiLink,
    Gitiki\Page,
    Gitiki\PathResolver,
    Gitiki\UrlGenerator;

use Symfony\Component\EventDispatcher\GenericEvent,
    Symfony\Component\Routing\Generator\UrlGenerator as RealUrlGenerator,
    Symfony\Component\Routing\RequestContext,
    Symfony\Component\Routing\Route,
    Symfony\Component\Routing\RouteCollection;

class WikiLinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideContent
     */
    public function testOnMeta($content, $expected, $comment)
    {
        $page = new Page('test');
        $page->setContent($content);

        $routes = new RouteCollection();
        $routes->add('page', new Route('/{path}.{_format}', [], [
            'path' => '[\w\d-/]+',
            '_format' => 'html',
        ]));
        $routes->add('page_dir', new Route('/{path}', [], [
            'path' => '([\w\d-/]+/|)$',
        ]));

        $requestContext = new RequestContext('/foo.php');
        $pathResolver = new PathResolver($requestContext);

        (new WikiLink(
            __DIR__.'/fixtures',
            $pathResolver,
            new UrlGenerator($pathResolver, new RealUrlGenerator($routes, $requestContext))
        ))->onContent(new GenericEvent($page));

        $this->assertSame($expected, $page->getContent(), $comment);
    }

    public function provideContent()
    {
        return [
            ['', '', 'Test with empty content'],
            ['<p><a href="bar.md">Bar page</a></p>', '<p><a href="/foo.php/bar.html">Bar page</a></p>', 'Test link to another wiki page'],
            ['<p><a href="hello.md">hello</a></p>', '<p><a href="/foo.php/hello.html" class="new">hello</a></p>', 'Test link to nonexistent wiki page'],
            ['<p><a href="foo.md#description">foo</a></p>', '<p><a href="/foo.php/foo.html#description" class="new">foo</a></p>', 'Test link with a fragment'],
            ['<p><a href="http://example.org/foo">hello</a></p>', '<p><a href="http://example.org/foo" class="external">hello</a></p>', 'Test link to other website'],
            ['<p><a href="brian.md">Où est Brian?</a></p>', '<p><a href="/foo.php/brian.html" class="new">Où est Brian?</a></p>', 'Test with utf-8 content'],
            ['<p><a href="/index.md">index</a></p>', '<p><a href="/foo.php/" class="new">index</a></p>', 'Test with index page'],
            ['<p><a href="dir/index.md">dir index</a></p>', '<p><a href="/foo.php/dir/" class="new">dir index</a></p>', 'Test with index page in directory'],
            ['<p><a href="#foo">foo part</a></p>', '<p><a href="#foo">foo part</a></p>', 'Test with only fragment part'],
        ];
    }
}
