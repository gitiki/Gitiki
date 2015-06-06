<?php

namespace Gitiki\Exception;

class PageNotFoundException extends \RuntimeException
{
    private $page;

    private $meta;

    public function __construct($page, $meta)
    {
        $this->page = $page;

        $this->meta = $meta;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getMeta($name, $default = null)
    {
        return isset($this->meta[$name]) ? $this->meta[$name] : $default;
    }
}
