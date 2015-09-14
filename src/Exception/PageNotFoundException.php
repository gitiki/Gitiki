<?php

namespace Gitiki\Exception;

class PageNotFoundException extends \RuntimeException
{
    private $page;

    public function __construct($page)
    {
        $this->page = $page;
    }

    public function getPage()
    {
        return $this->page;
    }
}
