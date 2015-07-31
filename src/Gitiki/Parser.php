<?php

namespace Gitiki;

class Parser extends \Parsedown
{
    public function text($text)
    {
        throw new \BadMethodCallException('You must use Parser::page() method.');
    }

    public function page(Page $page)
    {
        $page->setContent(parent::text($page->getContent()));
    }

    protected function blockHeader($line)
    {
        $header = parent::blockHeader($line);

        if (preg_match('/^(.+) \{#([\w-]+)\}$/', $header['element']['text'], $matches)) {
            $header['element']['text'] = $matches[1];
            $header['element']['attributes']['id'] = $matches[2];
        }

        return $header;
    }
}
