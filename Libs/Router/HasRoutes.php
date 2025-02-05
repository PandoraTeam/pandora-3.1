<?php
namespace Pandora3\Router;

/**
 * Trait HasRoutes
 * @package Pandora3\Router
 */
trait HasRoutes {

	/** @var Route[] */
	protected $routes = [];
	
	/**
	 * @param string $path
	 * @param mixed $handler
	 * @param string $name
	 * @return Route
	 */
	public function get(string $path, $handler, string $name = ''): Route {
		$route = $this->createRoute($path, Route::METHOD_FLAG_GET | Route::METHOD_FLAG_HEAD, $handler, $name);
		$this->addRoute($route);
		return $route;
	}
	
	/**
	 * @param string $path
	 * @param mixed $handler
	 * @param string $name
	 * @return Route
	 */
	public function post(string $path, $handler, string $name = ''): Route {
		$route = $this->createRoute($path, Route::METHOD_FLAG_POST, $handler, $name);
		$this->addRoute($route);
		return $route;
	}
	
	/**
	 * @param string $path
	 * @param mixed $handler
	 * @param string $name
	 * @return Route
	 */
	public function put(string $path, $handler, string $name = ''): Route {
		$route = $this->createRoute($path, Route::METHOD_FLAG_PUT, $handler, $name);
		$this->addRoute($route);
		return $route;
	}
	
	/**
	 * @param string $path
	 * @param mixed $handler
	 * @param string $name
	 * @return Route
	 */
	public function delete(string $path, $handler, string $name = ''): Route {
		$route = $this->createRoute($path, Route::METHOD_FLAG_DELETE, $handler, $name);
		$this->addRoute($route);
		return $route;
	}
	
	/**
	 * @param string $path
	 * @param mixed $handler
	 * @param string $name
	 * @return Route
	 */
	public function patch(string $path, $handler, string $name = ''): Route {
		$route = $this->createRoute($path, Route::METHOD_FLAG_PATCH, $handler, $name);
		$this->addRoute($route);
		return $route;
	}
	
	/**
	 * @param string $path
	 * @param mixed $handler
	 * @param string $name
	 * @return Route
	 */
	public function head(string $path, $handler, string $name = ''): Route {
		$route = $this->createRoute($path, Route::METHOD_FLAG_HEAD, $handler, $name);
		$this->addRoute($route);
		return $route;
	}

	/**
	 * @param string $path
	 * @param mixed $handler
	 * @param string $name
	 * @return Route
	 */
	public function any(string $path, $handler, string $name = ''): Route {
		$route = $this->createRoute($path, Route::METHOD_FLAG_ANY, $handler, $name);
		$this->addRoute($route);
		return $route;
	}

	/**
	 * @param string $path
	 * @param array|string $methods
	 * @param mixed $handler
	 * @param string $name
	 * @return Route
	 */
	public function add(string $path, $methods, $handler, string $name = ''): Route {
		if (is_string($methods)) {
			$methods = explode('|', $methods);
		}
		$route = $this->createRoute($path, Router::methodsToRouteFlags($methods), $handler, $name);
		$this->addRoute($route);
		return $route;
	}
	
	/**
	 * @param Route $route
	 */
	protected function addRoute(Route $route): void {
		$this->routes[] = $route;
	}
	
	/**
	 * @param string $path
	 * @param array $options
	 */
	public function resource(string $path, array $options): void {
		$pluralName = $options['plural'] ?? null;
		$singleName = $options['single'] ?? null;
		if (!$pluralName && !$singleName && count($options) === 2) {
			[$pluralName, $singleName] = $options;
		}
		$routeName = $options['routeName'] ?? $pluralName;
		
		if (is_null($pluralName)) {
			throw new \RuntimeException("Resource route option 'plural' is required");
		}
		/* if (is_null($singleName)) {
			throw new \RuntimeException("Resource route option 'single' is required");
		} */

		$this->get($path, $pluralName, $routeName . '.' . $pluralName);
		
		if (!is_null($singleName)) {
			$this->get($path . '/{id}', $singleName, $routeName . '.' . $singleName);
		}
		
		$this->get($path . '/create', 'createForm', $routeName . '.createForm');
		$this->post($path . '/create', 'create', $routeName . '.create');
		
		$this->get($path . '/{id}/update', 'updateForm', $routeName . '.updateForm');
		$this->post($path . '/{id}/update', 'update', $routeName . '.update');
		
		$this->post($path . '/{id}/delete', 'delete', $routeName . '.delete');
	}
	
	/**
	 * @param string $path
	 * @param int $methodFlags
	 * @param $handler
	 * @param string $name
	 * @return Route
	 */
	abstract protected function createRoute(string $path, int $methodFlags, $handler, $name = ''): Route;

}