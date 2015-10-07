<?php

namespace Gitiki;

use Symfony\Component\Routing\RouteCollection as BaseRouteCollection;
use Symfony\Component\Routing\Route;

class RouteCollection extends BaseRouteCollection
{
    /**
     * Adds a route before another.
     *
     * @param string $before The before route name
     * @param string $name   The route name
     * @param Route  $route  A Route instance
     *
     * @throws \InvalidArgumentException If the before route name cannot be found
     *
     */
    public function addBefore($before, $name, Route $route)
    {
        $newRoute = $route;
        foreach ($this->all() as $routeName => $route) {
            if (null !== $newRoute && $before === $routeName) {
                $this->add($name, $newRoute);
                $newRoute = null;
            }

            if (null === $newRoute) {
                // move the existing route onto the end of collection
                $this->add($routeName, $route);
            }
        }

        if (null !== $newRoute) {
            throw new \InvalidArgumentException(sprintf('The route "%s" cannot be added before "%s", because the route "%2$s" was not found.', $name, $before));
        }
    }
}
