<?php

namespace Gitiki\Controller;

use Gitiki\Gitiki;

class AssetsController extends AbstractAssetController
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
        return parent::sendFile($gitiki, __DIR__.'/../Resources/assets/'.$file);
    }
}
