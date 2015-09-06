<?php

namespace Gitiki\Test\Event\Listener;

use Gitiki\Event\Listener\Image,
    Gitiki\Page,
    Gitiki\PathResolver;

use Symfony\Component\EventDispatcher\GenericEvent,
    Symfony\Component\Routing\RequestContext;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideContent
     */
    public function testOnMeta($content, $expected, $comment)
    {
        $page = new Page('test');
        $page->setContent($content);

        (new Image(
            new PathResolver(new RequestContext('/foo.php'))
        ))->onContent(new GenericEvent($page));

        $this->assertSame($expected, $page->getContent(), $comment);
    }

    public function provideContent()
    {
        return [
            ['', '', 'Test with empty content'],
            ['<p><img src="bar.jpg" alt="bar image" /></p>', '<p><a href="/foo.php/bar.jpg"><img src="/foo.php/bar.jpg" alt="bar image"></a></p>', 'Test image without link'],
            ['<p><a href="bar"><img src="bar.jpg" alt="bar image" /></a></p>', '<p><a href="bar"><img src="/foo.php/bar.jpg" alt="bar image"></a></p>', 'Test image with link'],

            ['<p><img src="http://example.org/bar.jpg" alt="bar remote image" /></p>', '<p><img src="http://example.org/bar.jpg" alt="bar remote image"></p>', 'Test image with remote image'],

            ['<p><img src="bar.jpg?size=120" alt="bar resized image" /></p>', '<p><a href="/foo.php/bar.jpg"><img src="/foo.php/bar.jpg?size=120" alt="bar resized image"></a></p>', 'Test image with size parameter (width)'],
            ['<p><img src="bar.jpg?size=x120" alt="bar resized image" /></p>', '<p><a href="/foo.php/bar.jpg"><img src="/foo.php/bar.jpg?size=x120" alt="bar resized image"></a></p>', 'Test image with size parameter (height)'],
            ['<p><img src="bar.jpg?size=80x120" alt="bar cropped image" /></p>', '<p><a href="/foo.php/bar.jpg"><img src="/foo.php/bar.jpg?size=80x120" alt="bar cropped image"></a></p>', 'Test image with size parameter (crop)'],

            ['<p><img src="bar.jpg?link=no" alt="bar no link image" /></p>', '<p><img src="/foo.php/bar.jpg" alt="bar no link image"></p>', 'Test image without link'],
            ['<p><a href="foo"><img src="bar.jpg?link=no" alt="bar no link image" /></a></p>', '<p><a href="foo"><img src="/foo.php/bar.jpg" alt="bar no link image"></a></p>', 'Test image without link to image and specific link'],
            ['<p><img src="bar.jpg?size=120&amp;link=no" alt="bar no link with resize image" /></p>', '<p><img src="/foo.php/bar.jpg?size=120" alt="bar no link with resize image"></p>', 'Test image resize without link'],
        ];
    }
}
