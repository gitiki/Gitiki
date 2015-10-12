<?php

namespace Gitiki\Event\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\HttpKernel\Event\GetResponseEvent,
    Symfony\Component\HttpKernel\KernelEvents;


class PathFixer implements EventSubscriberInterface
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $attributes = $event->getRequest()->attributes;
        if (false === $attributes->has('path')) {
            return;
        }

        $path = $attributes->get('path');
        if (empty($path)) {
            $attributes->set('path', '/');

            return;
        } elseif ('/' !== $path{0}) {
            $path = '/'.$path;
        }

        if ('/' !== substr($path, -1) && in_array($attributes->get('_format'), ['html', 'md'], true)) {
            $path .= '.md';
        }

        $attributes->set('path', $path);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 8],
        ];
    }
}
