<?php

namespace Gitiki\Controller;

use Gitiki\Exception\InvalidSizeException,
    Gitiki\Gitiki,
    Gitiki\Image;

use Symfony\Component\HttpFoundation\Request;

class ImageController
{
    public function imageAction(Gitiki $gitiki, Request $request, $path, $_format)
    {
        $image = new Image($gitiki['wiki_path'], $path.'.'.$_format);
        if (false === $image->isFile() || false === $image->isReadable()) {
            $gitiki->abort(404, sprintf('The image "%s" was not found.', $image->getRelativePath()));
        }

        if ($request->query->has('details')) {
            return $gitiki['twig']->render('image.html.twig', [
                'page' => $image,
            ]);
        }

        $response = $gitiki->sendFile($image)->setMaxAge(0);

        if (!$response->isNotModified($request) && null !== $size = $request->query->get('size')) {
            try {
                $response
                    ->setFile($gitiki['image']->resize($image, $size), null, false, false)
                    ->deleteFileAfterSend(true)
                ;
            } catch (InvalidSizeException $e) {
            }
        }

        return $response;
    }
}
