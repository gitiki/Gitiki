<?php

namespace Gitiki\Controller;

use Gitiki\Gitiki;

class FontAwesomeController extends AbstractAssetController
{
    public function cssAction(Gitiki $gitiki)
    {
        return $this->sendFile($gitiki, 'css/font-awesome.min.css');
    }

    public function fontAction(Gitiki $gitiki, $_format)
    {
        return $this->sendFile($gitiki, 'fonts/fontawesome-webfont.'.$_format);
    }

    protected function sendFile(Gitiki $gitiki, $file)
    {
        return parent::sendFile($gitiki, __DIR__.'/../../vendor/fortawesome/font-awesome/'.$file);
    }
}
