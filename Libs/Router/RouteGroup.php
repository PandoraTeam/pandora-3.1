<?php
namespace Pandora3\Router;

/**
 * Class RouteGroup
 * @package Pandora3\Router
 */
class RouteGroup {
	
	use HasRoutes;
	
	/** @var string */
	protected $controller;
	
	/** @var array */
	protected $middlewares;

	/** @var string */
	protected $uriPrefix;
	
	/**
	 * RouteGroup constructor
	 * @param array $params
	 */
	public function __construct(array $params) {
		$this->controller = $params['controller'] ?? '';
		$this->middlewares = $params['middlewares'] ?? [];
		$uriPrefix = $params['prefix'] ?? '';
		$this->uriPrefix = $uriPrefix
			? preg_replace('#^/?#', '/', $uriPrefix) : '';
	}
	
	/**
	 * @return array
	 */
	public function getRoutes(): array {
		return $this->routes;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function createRoute(string $path, int $methodFlags, $handler, $name = ''): Route {
		$path = $this->uriPrefix . preg_replace('#^/?#', '/', $path);
		$pattern = Router::generatePattern($path);
		if ($this->controller && is_string($handler)) {
			$handler = [$this->controller, $handler];
		}
		$route = new Route($path, $pattern, $methodFlags, $handler, $name);
		if ($this->middlewares) {
			$route->middlewares(...$this->middlewares);
		}
		return $route;
	}
	
}