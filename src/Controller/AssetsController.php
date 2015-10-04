<?php

namespace Gitiki\Controller;

use Gitiki\Gitiki;

use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException,
    Symfony\Component\HttpFoundation\File\File;

class AssetsController
{
    public function mainCssAction(Gitiki $gitiki)
    {
        return $this->sendFile($gitiki, 'css/main.css');
    }

    public function bootstrapCssAction(Gitiki $gitiki)
    {
        return $this->sendFile($gitiki, 'bootstrap/css/bootstrap.css');
    }

    protected function sendFile(Gitiki $gitiki, $file)
    {
        try {
            $fileInfo = new File(__DIR__.'/../Resources/assets/'.$file);
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
