<?php

namespace EquivRoute\Adpater;

use EquivRoute\RouteCollector;
use EquivRoute\Dispatcher;

class EquivRoute extends _Abstract
{
    public function __construct($options = [], $routes = [])
    {
        $this->routes = $routes;
        $this->routeCollector = new RouteCollector;
        $this->dispatcher = $this->dispatcher();
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
