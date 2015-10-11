<?php

namespace Gitiki;

class PageNav implements \IteratorAggregate
{
    protected $nav = [];

    private $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->nav);
    }

    public function getPage()
    {
        return $this->page;
    }

    public function add($icon, $route, array $routeParams = null)
    {
        unset($this->nav[$icon]);

        $this->nav[$icon] = ['name' => $route, 'params' => $routeParams];
    }
}
