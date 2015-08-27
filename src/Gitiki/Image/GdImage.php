<?php

namespace Gitiki\Image;

class GdImage extends AbstractImage
{
    protected function doResize(\SplFileInfo $image, array $size)
    {
        list($srcW, $srcH) = getimagesize($image->getPathname());
        $srcX = $srcY = 0;

        $crop = true;
        if (INF === $size['width'] && INF !== $size['height']) { // compute width size
            $size['width'] = $this->getSizeRatio($srcH, $size['height'], $srcW);
            $crop = false;
        } elseif (INF === $size['height'] && INF !== $size['width']) { // compute height size
            $size['height'] = $this->getSizeRatio($srcW, $size['width'], $srcH);
            $crop = false;
        } elseif (INF !== $size['width'] && INF !== $size['height']) {
            $srcRatio = $srcW / $srcH;
            $destRation = $size['width'] / $size['height'];

            if ($srcRatio === $destRation) {
                $crop = false;
            }
        }

        if ($crop) { // crop and resize
            $ratioW = ($size['width'] * 100) / $srcW;
            $ratioH = ($size['height'] * 100) / $srcH;

            if ($ratioW > $ratioH) {
                $oldSrcH = $srcH;
                $srcH = $this->getSizeRatio($size['width'], $srcW, $size['height']);
                $srcY = ($oldSrcH - $srcH) / 2;
            } else {
                $oldSrcW = $srcW;
                $srcW = $this->getSizeRatio($size['height'], $srcH, $size['width']);
                $srcX = ($oldSrcW - $srcW) / 2;
            }
        }

        $resized = imagecreatetruecolor($size['width'], $size['height']);

        imagecopyresized(
            $resized, $this->loadImage($image),
            0, 0, $srcX, $srcY,
            isset($destW) ? $destW : $size['width'], isset($destH) ? $destH : $size['height'],
            $srcW, $srcH
        );

        // @todo save the image in memory to avoid to remove a temporary file
        $temp = new \SplFileInfo(tempnam(sys_get_temp_dir(), 'gitiki'));
        $this->saveImage($image, $temp, $resized);

        // delete image at end
        register_shutdown_function(function ($imageResized) {
            unlink($imageResized->getPathname());
        }, $temp);

        return $temp;
    }

    private function loadImage(\SplFileInfo $image)
    {
        switch ($image->getExtension()) {
            case 'jpg':
            case 'jpeg':
                return imagecreatefromjpeg($image->getPathname());

            case 'png':
                return imagecreatefrompng($image->getPathname());

            case 'gif':
                return imagecreatefromgif($image->getPathname());
        }

        throw new \InvalidArgumentException;
    }

    private function saveImage(\SplFileInfo $original, \SplFileInfo $destination, $image)
    {
        switch ($original->getExtension()) {
            case 'jpg':
            case 'jpeg':
                return imagejpeg($image, $destination->getPathname());

            case 'png':
                return imagepng($image, $destination->getPathname());

            case 'gif':
                return imagegif($image, $destination->getPathname());
        }
    }
}
