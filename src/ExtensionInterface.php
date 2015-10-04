<?php

namespace Gitiki;

interface ExtensionInterface
{
    public function register(Gitiki $gitiki, array $config);

    public function boot(Gitiki $gitiki);
}
