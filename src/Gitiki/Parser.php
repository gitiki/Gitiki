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
        $header = parent::blockHeader($line);

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
