<?php

namespace Gitiki\Test;

use Gitiki\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideLinks
     */
    public function testLink($link, $expected)
    {
        $parser = new Parser(null, '/index.php/');
        $this->assertSame($expected, $parser->text($link));
    }

    public function provideLinks()
    {
        return [
            ['[test label](test)', '<p><a href="/index.php/test" class="new">test label</a></p>', 'Test simple link'],
            ['[test label](test#anchor)', '<p><a href="/index.php/test#anchor" class="new">test label</a></p>', 'Test link with anchor'],
        ];
    }
}
