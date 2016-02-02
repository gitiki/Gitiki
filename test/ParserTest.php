<?php

namespace Gitiki\Test;

use Gitiki\Page;
use Gitiki\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testText()
    {
        $parser = new Parser();

        $this->setExpectedException('BadMethodCallException');
        $parser->text('foo');
    }

    public function testBlockHeader()
    {
        $parser = new Parser();
        $page = new Page('test');

        $page->setContent('# Hello World!');
        $parser->page($page);
        $this->assertSame('<h1 id="hello-world">Hello World!</h1>', $page->getContent(), 'Test without specific id');

        $page->setContent('# Hello World! {#hello}');
        $parser->page($page);
        $this->assertSame('<h1 id="hello">Hello World!</h1>', $page->getContent(), 'Test with specific id');
    }

    public function testBlockSetextHeaderWithoutHeader()
    {
        $parser = new Parser();
        $page = new Page('test');

        $page->setContent(<<<EOF
- Red
- Green
- Blue
EOF
);
        $parser->page($page);
        $this->assertSame(array(), $page->getToc());
    }

    public function testPage()
    {
        $parser = new Parser();
        $page = new Page('test');

        $page->setContent(<<<EOF
# Hello World!

## foo bar
EOF
);
        $parser->page($page);
        $this->assertSame([[
            'id' => 'hello-world',
            'text' => 'Hello World!',
            'children' => [[
                'id' => 'foo-bar',
                'text' => 'foo bar',
            ]]
        ]]  , $page->getToc());
    }

    /**
     * @see https://github.com/erusev/parsedown/issues/358
     * @see https://github.com/gitiki/Gitiki/issues/7
     */
    public function testPageWithoutDuplicatedLink()
    {
        $page = new Page('test');
        $page->setContent('[http://gitiki.org](http://gitiki.org/)');

        (new Parser())->page($page);

        $this->assertSame('<p><a href="http://gitiki.org/">http://gitiki.org</a></p>', $page->getContent());
    }
}
