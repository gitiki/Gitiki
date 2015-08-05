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
}
