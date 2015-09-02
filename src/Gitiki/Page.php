<?php

namespace Gitiki;

class Page
{
    private $name;

    private $metas;

    private $toc;

    private $content;

    private $document;

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

    public function getToc()
    {
        return $this->toc;
    }

    public function setToc(array $toc)
    {
        $this->toc = $toc;
    }

    public function getDocument()
    {
        if (!$this->document) {
            $this->document = new \DOMDocument();
            $this->document->loadHTML('<?xml encoding="UTF-8">'.$this->content);
        }

        return $this->document;
    }

    public function getContent()
    {
        if ($this->document) {
            $this->content = null;

            $html = $this->document->childNodes->item(2);
            if ($html) {
                $nodes = $html
                    ->firstChild // body
                    ->childNodes // body child
                ;

                foreach ($nodes as $node) {
                    $this->content .= $this->document->saveHTML($node);
                }
            }
        }

        return $this->content ?: '';
    }

    public function setContent($content)
    {
        $this->content = empty($content) ? null : $content;
        $this->document = null;
    }
}
