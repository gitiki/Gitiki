<?php

namespace Gitiki;

class Page
{
    private $name;

    private $metas;

    private $toc;

    private $content;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTitle()
    {
        return $this->getMeta('title');
    }

    public function getMeta($meta)
    {
        return isset($this->metas[$meta]) ? $this->metas[$meta] : null;
    }

    public function getMetas()
    {
        return $this->metas;
    }

    public function setMetas($metas)
    {
        $this->metas = $metas;
    }

    public function setToc(array $toc)
    {
        $this->toc = $toc;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }
}
