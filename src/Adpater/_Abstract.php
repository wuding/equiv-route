<?php

namespace EquivRoute\Adpater;

class _Abstract
{
    public $routeCollector = null;
    public $dispatcher = null;

    public function callback($r, $routes = [])
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

    

    public function dispatch($httpMethod, $uri)
    {
        return $this->routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);
    }
}
