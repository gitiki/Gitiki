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

        $page->setContent(
            $this->fixIssue358(parent::text($page->getContent()))
        );
        $page->setToc($this->tocBuilder->getToc());

        $this->tocBuilder = null;
    }

    protected function blockHeader($line)
    {
        return $this->addHeaderInToc(parent::blockHeader($line));
    }

    protected function blockSetextHeader($line, array $block = null)
    {
        $header = parent::blockSetextHeader($line, $block);
        if (null !== $header) {
            $header = $this->addHeaderInToc();
        }

        return $header;
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

    /**
     * @see https://github.com/erusev/parsedown/issues/358
     */
    private function fixIssue358($content)
    {
        if (empty($content)) {
            return $content;
        }

        $document = new \DOMDocument();
        $document->loadXML('<xml>'.$content.'</xml>');

        $xpath = new \DOMXPath($document);
        $badLinks = $xpath->query('//a[a]');
        if (0 === $badLinks->length) {
            return $content;
        }

        // iterate on links containing link
        foreach ($badLinks as $link) {
            foreach ($link->childNodes as $node) {
                // test if is a link node
                if ($node instanceof \DOMElement && 'a' === $node->tagName) {
                    $link->insertBefore($node->childNodes->item(0), $node);
                    $link->removeChild($node);
                }
            }
        }

        $content = '';
        foreach ($xpath->query('//xml/*') as $node) {
            $content .= $document->saveXML($node);
        }

        return $content;
    }
}
