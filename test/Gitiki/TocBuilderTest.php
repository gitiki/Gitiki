<?php

namespace Gitiki\Test;

use Gitiki\TocBuilder;

class TocBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider headerDataProvider
     */
    public function testAdd(array $headers, array $toc, $message)
    {
        $builder = new TocBuilder();

        foreach ($headers as $header) {
            call_user_func_array([$builder, 'add'], $header);
        }

        $this->assertSame($toc, $builder->getToc(), $message);
    }

    public function headerDataProvider()
    {
        return [
            [
                [
                    [1, 'Hello World!'],
                    [2, 'Foo bar', 'foo'],
                    [3, 'Test'],
                    [2, 'Bar foo', 'bar'],
                    [3, 'Test'],
                ], [[
                    'id' => 'hello-world',
                    'text' => 'Hello World!',
                    'children' => [[
                        'id' => 'foo',
                        'text' => 'Foo bar',
                        'children' => [[
                            'id' => 'test',
                            'text' => 'Test',
                        ]]
                    ], [
                        'id' => 'bar',
                        'text' => 'Bar foo',
                        'children' => [[
                            'id' => 'test-2',
                            'text' => 'Test',
                        ]]
                    ]]
                ]], 'Test with first level'
            ], [
                [
                    [2, 'Foo bar', 'foo'],
                    [3, 'Test'],
                    [2, 'Bar foo', 'bar'],
                    [3, 'Test'],
                ], [[
                    'id' => 'foo',
                    'text' => 'Foo bar',
                    'children' => [[
                        'id' => 'test',
                        'text' => 'Test',
                    ]]
                ], [
                    'id' => 'bar',
                    'text' => 'Bar foo',
                    'children' => [[
                        'id' => 'test-2',
                        'text' => 'Test',
                    ]]
                ]], 'Test without first level'
            ],
        ];
    }
}
