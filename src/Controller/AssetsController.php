<?php

namespace Gitiki\Controller;

use Silex\Application;

use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException,
    Symfony\Component\HttpFoundation\File\File;

class AssetsController
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function mainCssAction()
    {
        return $this->sendFile('css/main.css');
    }

    public function bootstrapCssAction()
    {
        return $this->sendFile('bootstrap/css/bootstrap.css');
    }

    protected function sendFile($file)
    {
        try {
            $fileInfo = new File(__DIR__.'/../Resources/assets/'.$file);
        } catch (FileNotFoundException $e) {
            $this->app->abort(404, 'The file "%s" does not exists');
        }

        $response = $this->app->sendFile($fileInfo)->setMaxAge(0);

        $request = $this->app['request'];
        if (!$response->isNotModified($request)) {
            $response->headers->set('content-type', $request->getMimeType($fileInfo->getExtension()));
        }

        return $response;
    }
}
