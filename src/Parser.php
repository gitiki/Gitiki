<?php

namespace Gitiki;

class Parser extends \Parsedown
{
    protected $tocBuilder;

    public function text($text)
    {
        throw new \BadMethodCallException('You must use Parser::page() method.');
    }

    public function page(Page $page)
    {
        $this->tocBuilder = new TocBuilder();

        $page->setContent(parent::text($page->getContent()));
        $page->setToc($this->tocBuilder->getToc());

        $this->tocBuilder = null;
    }

    protected function blockHeader($line)
    {
        return $this->addHeaderInToc(parent::blockHeader($line));
    }

    protected function blockSetextHeader($line, array $block = null)
    {
        return $this->addHeaderInToc(parent::blockSetextHeader($line, $block));
    }

    protected function blockTable($line, array $block = null)
    {
        $table = parent::blockTable($line, $block);

        if (null !== $table) {
            $table['element']['attributes']['class'] = 'table table-striped';
        }

        return $table;
    }

    protected function addHeaderInToc(array $header)
    {
        if (preg_match('/^(.+) \{#([\w-]+)\}$/', $header['element']['text'], $matches)) {
            $text = $matches[1];
            $id = $matches[2];
        } else {
            $text = $header['element']['text'];
            $id = null;
        }

        $header['element']['text'] = $text;
        $header['element']['attributes']['id'] = $this->tocBuilder->add(
            (int) $header['element']['name']{1}, $text, $id
        );

        return $header;
    }
}
