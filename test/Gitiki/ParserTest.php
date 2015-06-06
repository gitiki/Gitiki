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
        $parser = new Parser(null, '/');
        $this->assertSame($expected, $parser->text($link));
    }

    public function provideLinks()
    {
        return [
            ['[[test]]', '<p><a href="/test" class="new">test</a></p>', 'Test simple link'],
            ['[[test#anchor]]', '<p><a href="/test#anchor" class="new">test</a></p>', 'Test link with anchor'],

            ['[[test|Test]]', '<p><a href="/test" class="new">Test</a></p>', 'Test link with custom text'],
            ['[[test#anchor|Test]]', '<p><a href="/test#anchor" class="new">Test</a></p>', 'Test link with anchor and custom text'],

            # escape char is needed for link in table
            ['[[test\|Test]]', '<p><a href="/test" class="new">Test</a></p>', 'Test link with custom text and escape char'],
            ['[[test#anchor\|Test]]', '<p><a href="/test#anchor" class="new">Test</a></p>', 'Test link with anchor and custom text and escape char'],
        ];
    }
}
