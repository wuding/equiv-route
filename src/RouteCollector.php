<?php

namespace EquivRoute;

class RouteCollector
{
    public function __construct()
    {

    }

    public function getData()
    {
        return [];
    }

    public function addRoute($httpMethod = '', $route = '', $handler = '')
    {
        global $_ROUTE;
        $_ROUTE[] = array($httpMethod, $route, $handler);
    }
}
