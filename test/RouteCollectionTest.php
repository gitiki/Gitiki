<?php

namespace Gitiki\Test;

use Gitiki\RouteCollection;
use Symfony\Component\Routing\Route;

class RouteCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddBefore()
    {
        $foo = new Route('/foo');
        $bar = new Route('/bar');
        $helloWorld = new Route('/hello-world');

        $collection = new RouteCollection();
        $collection->add('foo', $foo);
        $collection->add('bar', $bar);
        $this->assertSame(['foo' => $foo, 'bar' => $bar], $collection->all());

        $collection->addBefore('bar', 'hello_world', $helloWorld);
        $this->assertSame(['foo' => $foo, 'hello_world' => $helloWorld, 'bar' => $bar], $collection->all());
    }

    public function testAddBeforeWithNonexistentBeforeRouteName()
    {
        $collection = new RouteCollection();

        $this->setExpectedException('InvalidArgumentException');
        $collection->addBefore('nonexistent', 'foo', new Route('/foo'));
    }
}
