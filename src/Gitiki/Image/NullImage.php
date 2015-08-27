<?php

namespace Gitiki\Image;

class NullImage implements ImageInterface
{
    public function resize(\SplFileInfo $image, $size)
    {
        return $image;
    }
}
