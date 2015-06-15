<?php

namespace Gitiki\Test\Event\Listener;

use Gitiki\Event\Listener\Metadata,
    Gitiki\Page;

use Symfony\Component\EventDispatcher\GenericEvent;

class MetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideContent
     */
    public function testOnLoad($content, $expectedMetas, $expectedContent, $comment)
    {
        $page = new Page('test');
        $page->setContent($content);

        (new Metadata())->onLoad(new GenericEvent($page));

        $this->assertSame($expectedMetas, $page->getMetas(), $comment);
        $this->assertSame($expectedContent, $page->getContent(), $comment);
    }

    /**
     * @dataProvider provideMetas
     */
    public function testOnMeta($metas, $comment)
    {
        $page = new Page('test');
        $page->setMetas($metas);

        (new Metadata())->onMeta(new GenericEvent($page));

        $this->assertInternalType('array', $page->getMetas(), $comment);
        $this->assertEquals(['foo' => 'bar'], $page->getMetas(), $comment);
    }

    public function provideContent()
    {
        return [
            [<<<EOF
~~~
foo: bar
~~~
EOF
, 'foo: bar', null, 'Test without content'],
            [<<<EOF
~~~
bar: foo
~~~
# Hello World!
EOF
, 'bar: foo', '# Hello World!', 'Test with content'],
            [<<<EOF
~~~
bar: foo
~~~

# Hello World!
EOF
, 'bar: foo', "\n".'# Hello World!', 'Test with blank line before content'],
            [<<<EOF
# Hello World!
EOF
, null, '# Hello World!', 'Test without metas']
        ];
    }

    public function provideMetas()
    {
        return [
            ['foo: bar', 'Test string meta'],
            [['foo' =>  'bar'], 'Test array meta'],
        ];
    }
}
