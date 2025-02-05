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
	protected $baseUriPrefix;

	/**
	 * RouteGroup constructor
	 * @param array $params
	 * @param string $baseUri
	 */
	public function __construct(array $params, string $baseUri) {
		$this->controller = $params['controller'] ?? '';
		$this->middlewares = $params['middlewares'] ?? [];
		$uriPrefix = $params['prefix'] ?? '';

		$baseUriPrefix = ($baseUri === '/') ? '' : $baseUri;
		if ($uriPrefix) {
			// add leading slash to uriPrefix
			$uriPrefix = $baseUriPrefix . preg_replace('#^/?#', '/', $uriPrefix);
			$baseUriPrefix = preg_replace('#/$#', '', $uriPrefix); // remove trailing slash
		}
		$this->baseUriPrefix = $baseUriPrefix;
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
		/* // add leading slash to path
		$path = $this->uriPrefix . preg_replace('#^/?#', '/', $path); */
		
		// path should always start with slash
		// remove trailing slash, but keep it if path equal '/'
		$path = preg_replace('#(.+)/$#', '$1', $this->baseUriPrefix . $path);
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