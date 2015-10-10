<?php

namespace Gitiki\Test;

use Gitiki\Route;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTrueConditions
     */
    public function testCondition($key, $regexp, array $queries, $name)
    {
        $queries = !isset($queries[0]) ? [$queries] : $queries;
        foreach ($queries as $query) {
            if (isset($query[0])) {
                $resultTest = $query[1];
                $query = $query[0];
            } else {
                $resultTest = true;
            }

            $result = (new ExpressionLanguage())->evaluate(
                (new Route())->assertGet($key, $regexp)->getCondition(),
                ['request' => new Request($query)]
            );

            call_user_func([$this, $resultTest ? 'assertTrue' : 'assertFalse'], $result, $name);
        }
    }

    public function provideTrueConditions()
    {
        return [
            ['foo', 'bar', ['foo' => 'bar'], 'Test with simple text'],
            ['foo', '', ['foo' => ''], 'Test with empty value'],
            ['foo', '/bar/', ['foo' => '/bar/'], 'Test with slash chars'],
            ['foo', 'ba?r', [
                ['foo' => 'bar'],
                ['foo' => 'br'],
            ], 'Test with optional char'],
            ['foo', 'b(a|i|o)r', [
                ['foo' => 'bar'],
                ['foo' => 'bir'],
                ['foo' => 'bor'],
            ], 'Test with alternative char'],
            ['foo', 'b\(ar', ['foo' => 'b(ar'], 'Test with real bracket'],
            ['foo', 'b\(?ar', [
                ['foo' => 'b(ar'],
                ['foo' => 'bar'],
            ], 'Test with real bracket optional'],
            ['foo', 'ba{2,5}r', [
                ['foo' => 'baar'],
                ['foo' => 'baaar'],
                ['foo' => 'baaaar'],
                ['foo' => 'baaaaar'],
                [['foo' => 'bar'], false],
                [['foo' => 'baaaaaar'], false],
            ], 'Test with quantifier'],
            ['foo', 't^es$t', ['foo' => 't^es$t'], 'Test with start and end regexp char']
        ];
    }
}
