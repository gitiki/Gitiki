<?php

namespace Gitiki;

class Image extends \SplFileInfo
{
    private $relativePath;

    private $imageSize;

    public function __construct($basePath, $relativePath)
    {
        parent::__construct($basePath.'/'.$relativePath);

        $this->relativePath = $relativePath;
    }

    public function getTitle()
    {
        return basename($this->relativePath);
    }

    public function getRelativePath()
    {
        return $this->relativePath;
    }

    public function getImageSize()
    {
        if (!$this->imageSize) {
            list($this->imageSize[0], $this->imageSize[1]) = getimagesize($this->getPathname());
        }

        return $this->imageSize;
    }
}
