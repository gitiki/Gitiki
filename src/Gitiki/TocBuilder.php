<?php

namespace Gitiki;

use HtmlTools\Inflector;

class TocBuilder
{
    private $ids;

    private $previousLevel;

    private $levels;

    private $toc;

    public function __construct()
    {
        $this->ids =
        $this->toc = [];

        $this->levels = array_fill(1, 6, 0);
        $this->previousLevel = 0;
    }

    public function getToc()
    {
        if (1 === count($this->toc) && !isset($this->toc[0]['id'])) {
            return $this->toc[0]['children'];
        }

        return $this->toc;
    }

    public function add($level, $text, $id = null)
    {
        $this->fixLevel($level);

        $id = null === $id ? $this->getId($text) : $this->fixId($id);

        $toc = &$this->getTocLevel($level);
        $toc[] = [
            'id' => $id,
            'text' => $text,
        ];

        return $id;
    }

    protected function getId($text)
    {
        return $this->fixId(Inflector::urlize($text));
    }

    protected function fixId($id)
    {
        if (isset($this->ids[$id])) {
            $this->ids[$id]++;
            $id .= '-'.$this->ids[$id];
        } else {
            $this->ids[$id] = 1;
        }

        return $id;
    }

    protected function fixLevel($currentLevel)
    {
        if ($this->previousLevel > $currentLevel) {
            // increment current level
            $this->levels[$currentLevel]++;

            // reset sublevels
            for ($i = $currentLevel + 1; $i <= 6; $i++) {
                $this->levels[$i] = 0;
            }
        }

        $this->previousLevel = $currentLevel;
    }

    protected function &getTocLevel($level)
    {
        $toc = &$this->toc;
        for ($i = 1; $i < $level; $i++) {
            $toc = &$toc[$this->levels[$i]]['children'];
        }

        return $toc;
    }
}
