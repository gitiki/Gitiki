<?php

namespace Gitiki\Event\Listener;

use Gitiki\Gitiki;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\HttpKernel\Event\GetResponseEvent,
    Symfony\Component\HttpKernel\HttpKernelInterface,
    Symfony\Component\HttpKernel\KernelEvents,
    Symfony\Component\Routing\RouteCollection;

class RedirectIndexHtml implements EventSubscriberInterface
{
    protected $gitiki;

    public function __construct(Gitiki $gitiki)
    {
        $this->gitiki = $gitiki;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $attributes = $event->getRequest()->attributes;

        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        } elseif (false === $attributes->has('_format') || 'html' !== $attributes->get('_format')) {
            return;
        } elseif (false === $attributes->has('path')) {
            return;
        } elseif (null === $ifIndex = $this->gitiki['routes']->get($attributes->get('_route'))->getOption('_if_index')) {
            return;
        } elseif (!preg_match('#^(.*/)index\.md$#', $attributes->get('path'), $match)) {
            return;
        }

        $event->setResponse($this->gitiki->redirect($this->gitiki->path(
            $ifIndex[0], $ifIndex[1]($event->getRequest())
        ), 301));
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
