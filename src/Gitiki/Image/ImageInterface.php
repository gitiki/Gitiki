<?php

namespace Gitiki\Image;

interface ImageInterface
{
    public function resize(\SplFileInfo $image, $size);
}
