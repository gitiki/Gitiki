<?php

namespace Gitiki\Controller;

use Gitiki\Gitiki;

use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException,
    Symfony\Component\HttpFoundation\File\File;

abstract class AbstractAssetController
{
    protected function sendFile(Gitiki $gitiki, $file)
    {
        try {
            $fileInfo = new File($file);
        } catch (FileNotFoundException $e) {
            $gitiki->abort(404, 'The file "%s" does not exists');
        }

        $response = $gitiki->sendFile($fileInfo)->setMaxAge(0);

        $request = $gitiki['request'];
        if (!$response->isNotModified($request)) {
            $response->headers->set('content-type', $request->getMimeType($fileInfo->getExtension()));
        }

        return $response;
    }
}
