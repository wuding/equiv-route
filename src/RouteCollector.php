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
        global $_EQUIV_ROUTE;
        $_EQUIV_ROUTE[] = array($httpMethod, $route, $handler);
    }
}
