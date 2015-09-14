<?php

namespace Gitiki\Image;

use Gitiki\Image;

interface ImageInterface
{
    public function resize(Image $image, $size);
}
