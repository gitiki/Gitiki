<?php

namespace Gitiki\Exception;

class PageRedirectedException extends \RuntimeException
{
    private $page;

    private $target;

    public function __construct($page, $target)
    {
        $this->page = $page;

        $this->target = $target;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getTarget()
    {
        return $this->target;
    }
}
