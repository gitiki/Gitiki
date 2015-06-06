<?php

namespace Gitiki;

use Symfony\Component\Yaml\Yaml;

class Parser extends \Parsedown
{
    protected $wikiDir;

    protected $baseUri;

    protected $metas;

    public function __construct($wikiDir, $baseUri)
    {
        $this->wikiDir = $wikiDir;
        $this->baseUri = $baseUri;
        $this->metas = [];
    }

    public function parsePage($page)
    {
        $pagePath = $this->wikiDir.'/'.$page.'.md';
        if (!is_file($pagePath)) {
            throw new Exception\PageNotFoundException($page, $this->getMeta($page));
        }

        $content = $this->text(file_get_contents($pagePath));

        if (false !== $meta = $this->getMeta($page)) {
            $content = $this->text('# '.$meta['title'])."\n\n".$content;
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
        $extent = null;
        $new = null;

        // Test wiki link syntax
        // Read meta file: [[article_file]]
        // Set title: [[article_file|label]]
        $excerpt['text'] = preg_replace_callback('/^\[\[([^\]|\\\#)]+)(#[^\]|\\\)]+)?(?:\\\?\|([^\]]+))?\]\]/', function ($matches) use (&$extent, &$new) {
            $extent = strlen($matches[0]);

            $uri = $this->baseUri.$matches[1];
            if (isset($matches[2])) {
                $uri .= $matches[2];
            }

            $meta = $this->getMeta($matches[1]);
            if (false === $meta) {
                $new = true;
            }

            if (false === isset($matches[3])) {
                if (isset($meta['title'])) {
                    $matches[3] = $meta['title'];
                } else {
                    $matches[3] = $matches[1];
                }
            }

            return sprintf('[%s](%s)', $matches[3], $uri);
        }, $excerpt['text'], 1);

        $link = parent::inlineLink($excerpt);

        if (null !== $link) {
            if (null !== $extent) {
                $link['extent'] = $extent;
            }

            if (true === $new) {
                $link['element']['attributes']['class'] = 'new';
            }
        }

        return $link;
    }

    protected function getMeta($page)
    {
        if (isset($this->metas[$page])) {
            return $this->metas[$page];
        }

        $metaFile = $this->wikiDir.'/'.$page.'.meta';

        return $this->metas[$page] = is_file($metaFile) ? Yaml::parse(file_get_contents($metaFile)) : false;
    }
}
