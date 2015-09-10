<?php

namespace Gitiki\Controller;

use Gitiki\Exception\InvalidSizeException,
    Gitiki\Gitiki,
    Gitiki\Image;

use Symfony\Component\HttpFoundation\Request;

class ImageController
{
    private $gitiki;

    public function __construct(Gitiki $gitiki)
    {
        $this->gitiki = $gitiki;
    }

    public function imageAction(Request $request, $path, $_format)
    {
        $image = new Image($this->gitiki['wiki_dir'], $path.'.'.$_format);
        if (false === $image->isFile() || false === $image->isReadable()) {
            $this->gitiki->abort(404, sprintf('The image "%s" was not found.', $image->getRelativePath()));
        }

        if ($request->query->has('details')) {
            return $this->gitiki['twig']->render('image.html.twig', [
                'page' => $image,
            ]);
        }

        if (null !== $size = $request->query->get('size')) {
            try {
                return $this->gitiki->sendFile($this->gitiki['image']->resize($image, $size));
            } catch (InvalidSizeException $e) {
            }
        }

        return $this->gitiki->sendFile($image);
    }
}
