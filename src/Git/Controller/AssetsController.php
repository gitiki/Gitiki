<?php

namespace Gitiki\Git\Controller;

use Gitiki\Controller\AbstractAssetController,
    Gitiki\Gitiki;

class AssetsController extends AbstractAssetController
{
    public function cssAction(Gitiki $gitiki)
    {
        return $this->sendFile($gitiki, __DIR__.'/../Resources/assets/css/git.css');
    }
}
