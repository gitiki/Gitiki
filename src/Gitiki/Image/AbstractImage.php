<?php

namespace Gitiki\Image;

use Gitiki\Exception\InvalidSizeException;

abstract class AbstractImage implements ImageInterface
{
    abstract protected function doResize(\SplFileInfo $image, array $size);

    public function resize(\SplFileInfo $image, $size)
    {
        $sizeParsed = $this->parseSize($size);
        if (INF === $sizeParsed['width'] && INF === $sizeParsed['height']) {
            throw new InvalidSizeException(sprintf('The size "%s" has been invalid.', $size));
        }

        return $this->doResize($image, $sizeParsed);
    }

    protected function parseSize($size)
    {
        if (!preg_match('/^(?<width>\d+)?(?:x(?<height>\d+))?$/', $size, $match)) {
            throw new InvalidSizeException(sprintf('The size "%s" cannot be parsed!', $size));
        }

        if (empty($match['width'])) {
            $match['width'] = INF;
        } else {
            $match['width'] = (int) $match['width'];
        }

        if (empty($match['height'])) {
            $match['height'] = INF;
        } else {
            $match['height'] = (int) $match['height'];
        }

        return [
            'width' => $match['width'],
            'height' => $match['height'],
        ];
    }

    protected function getSizeRatio($originalSize, $destinationSize, $sizeToCompute)
    {
        return (int) round($sizeToCompute / ($originalSize / $destinationSize));
    }
}
