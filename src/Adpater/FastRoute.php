<?php

namespace EquivRoute\Adpater;

use FastRoute\Dispatcher\GroupCountBased as Dispatcher;

class FastRoute
{
    public $routeCollector = null;
    public $dispatcher = null;

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

    public function callback(\FastRoute\RouteCollector $r, $routes = [])
    {
        # print_r($r);
        if (is_array($routes)) {
            foreach ($routes as $route) {
                $r->addRoute($route[0], $route[1], $route[2]);
            }
        }
    }

    public function addRoute($httpMethod, $route, $handler)
    {
        $this->routeCollector->addRoute($httpMethod, $route, $handler);
    }

    public function getData()
    {
        return $this->routeCollector->getData();
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

    public function dispatch($httpMethod, $uri)
    {
        return $this->routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);
    }
}
