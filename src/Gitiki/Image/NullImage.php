<?php

namespace Gitiki\Image;

use Gitiki\Image;

class NullImage implements ImageInterface
{
    public function resize(Image $image, $size)
    {
        return $image;
    }
}
