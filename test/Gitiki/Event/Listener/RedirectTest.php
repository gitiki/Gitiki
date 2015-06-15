<?php

namespace Gitiki\Test\Event\Listener;

use Gitiki\Event\Listener\Redirect,
    Gitiki\Page;

use Symfony\Component\EventDispatcher\GenericEvent;

class RedirectTest extends \PHPUnit_Framework_TestCase
{
    public function testOnMeta()
    {
        $page = new Page('test');
        $page->setMetas(['title' => 'Hello World!']);

        $event = new GenericEvent($page);
        $redirect = new Redirect();

        // no redirect
        $redirect->onMeta(new GenericEvent($page));

        // set meta redirect
        $page->setMetas(['redirect' => 'foobar']);

        $this->setExpectedException('Gitiki\\Exception\\PageRedirectedException');
        $redirect->onMeta($event);
    }
}
