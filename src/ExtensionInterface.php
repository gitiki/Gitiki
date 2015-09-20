<?php

namespace Gitiki;

use Silex\ServiceProviderInterface;

interface ExtensionInterface extends ServiceProviderInterface
{
    public static function getConfigurationKey();
}
