<?php

namespace EquivRoute;

class Router
{
    public $adpater = null;
    public static $uri = '';

    public function __construct($name = 'wuding/equiv-route', $routes = [], $options = [])
    {
        $names = [
            'wuding/equiv-route' => 'EquivRoute',
            'nikic/fast-route' => 'FastRoute',
        ];

        if (array_key_exists($name, $names)) {
            $file = $names[$name];
            $class = "EquivRoute\\Adpater\\$file";
            $this->adpater = new $class($options, $routes);

            if ($routes) {
                $this->addRoutes($routes);
            }
        }
    }

    public function addRoutes($routes)
    {
        if (is_array($routes)) {
            foreach ($routes as $route) {
                $r = array('', '', '');
                $rt = $this->arr_merge($r, $route);
                $this->addRoute($rt[0], $rt[1], $rt[2]);
            }
        }
    }

    public function arr_merge($arr, $two)
    {
        foreach ($arr as $key => $value) {
            $arr[$key] = isset($two[$key]) ? $two[$key] : $value;
        }
        return $arr;
    }

    public function addRoute($httpMethod, $route, $handler)
    {
        $this->adpater->addRoute($httpMethod, $route, $handler);
    }

    public function getData()
    {
        return $this->adpater->getData();
    }

    public function addGroup()
    {
    }

    public function parse()
    {
    }

    public function dispatch($httpMethod, $uri, $status = -2)
    {
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        global $_VAR;
        self::$uri = $_VAR['uri'] = $uri = rawurldecode($uri);
        return $this->adpater->dispatch($httpMethod, $uri, $status);
    }
}
