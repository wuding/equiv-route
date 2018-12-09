<?php

namespace EquivRoute;

class Router
{
	public $adpater = null;

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
				# $this->addRoutes($routes);
			}
		}
	}

	public function addRoutes($routes)
	{
		if (is_array($routes)) {
			foreach ($routes as $route) {
				$this->addRoute($route[0], $route[1], $route[2]);
			}
		}
		
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
	
	public function dispatch($httpMethod, $uri)
	{
		return $this->adpater->dispatch($httpMethod, $uri);
	}
}
