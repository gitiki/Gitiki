<?php

namespace Gitiki;

use Symfony\Component\Routing\RequestContext;

class PathResolver
{
    protected $context;

    private $pathInfo;
    private $baseDirnameParts;

    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function getBaseUrl()
    {
        return $this->context->getBaseUrl();
    }

    public function resolve($path)
    {
        if ($path === '/index.md') {
            return '';
        }

        $pathParts = explode('/', dirname($path));

        if ('.' === $pathParts[0] || '..'  === $pathParts[0] || '' !== $pathParts[0]) {
            $newPathParts = $this->getBaseDirnameParts();
        } else {
            $newPathParts = [];
        }

        foreach ($pathParts as $part) {
            if ('..' === $part) {
                array_pop($newPathParts);
            }

            if ('.' === $part || '..' === $part || '' === $part) {
                continue;
            }

            $newPathParts[] = $part;
        }

        $pathResolved = empty($newPathParts) ? '' : implode('/', $newPathParts).'/';

        $filename = basename($path);
        if ('index.md' !== $filename) {
            $pathResolved .= $filename;
        }

        return $pathResolved;
    }

    private function getBaseDirnameParts()
    {
        if ($this->pathInfo !== $this->context->getPathInfo()) {
            $this->pathInfo = $this->context->getPathInfo();

            $dirname = dirname($this->pathInfo);
            if ('/' === $dirname) {
                // create empty array because explode create an array with two entries empty...
                $this->baseDirnameParts = [];
            } else {
                $this->baseDirnameParts = explode('/', $dirname);

                // remove the first entry because it is an empty entry
                array_shift($this->baseDirnameParts);
            }
        }

        return $this->baseDirnameParts;
    }
}
