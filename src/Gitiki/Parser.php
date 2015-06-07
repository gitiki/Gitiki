<?php

namespace Gitiki;

use Symfony\Component\Yaml\Yaml;

class Parser extends \Parsedown
{
    protected $wikiDir;

    protected $baseUri;

    public function __construct($wikiDir, $baseUri)
    {
        $this->wikiDir = $wikiDir;
        $this->baseUri = $baseUri;
    }

    public function parsePage($page)
    {
        $pagePath = $this->getPagePath($page);
        if (false === $pagePath) {
            throw new Exception\PageNotFoundException($page);
        }

        if (preg_match('/^~{3,}\n(.+)~{3,}\n(.*)$/sU', file_get_contents($pagePath), $matches)) {
            $meta = $this->parseMeta($matches[1]);

            if (isset($meta['redirect'])) {
                throw new Exception\PageRedirectedException($page, $meta['redirect']);
            }
        }

        $content = $this->text($matches[2]);

        if (isset($meta['title'])) {
            $content = $this->text('# '.$meta['title']).$content;
        }

        return $content;
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

    protected function inlineLink($excerpt)
    {
        $link = parent::inlineLink($excerpt);
        if (null === $link) {
            return;
        }

        $url = parse_url($link['element']['attributes']['href']);
        if (!isset($url['host'])) {
            if (false === $this->getPagePath($url['path'])) {
                $link['element']['attributes']['class'] = 'new';
            }

            $link['element']['attributes']['href'] = $this->baseUri.$link['element']['attributes']['href'];
        }

        return $link;
    }

    protected function parseMeta($text)
    {
        return Yaml::parse($text);
    }

    protected function getPagePath($page)
    {
        if (!is_file($pagePath = $this->wikiDir.'/'.$page.'.md')) {
            return false;
        }

        return $pagePath;
    }
}
