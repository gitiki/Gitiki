<?php

namespace Gitiki\Extension;

use Gitiki\Gitiki;

interface WebpackInterface
{
    public function getWebpackEntries(Gitiki $gitiki);
}
