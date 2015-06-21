<?php

namespace Gitiki;

class Parser extends \Parsedown
{
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
