<?php

namespace EquivRoute\Adpater;

use FastRoute\Dispatcher\GroupCountBased as Dispatcher;

class FastRoute extends _Abstract
{
    public function __construct($options = [], $routes = [])
    {
        $this->routes = $routes;
        $callback = function(\FastRoute\RouteCollector $r) {
            $this->callback($r, $this->routes);
            $this->routeCollector = $r;
        };

        if (isset($options['cacheFile']) && $options['cacheFile']) {
            $this->dispatcher = \FastRoute\cachedDispatcher($callback, $options);
        } else {
            $this->dispatcher = \FastRoute\simpleDispatcher($callback, $options);
        }
    }

    public function dispatcher($data = null)
    {
        if (null === $data) {
            if (null !== $this->dispatcher) {
                return $this->dispatcher;
            }

            $data = $this->getData();
        }
        return $this->dispatcher = new Dispatcher($data);
    }
}
