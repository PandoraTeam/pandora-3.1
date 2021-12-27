<?php
namespace Pandora3\Router;

use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\RequestHandlerInterface;
use Pandora3\Contracts\RouteResolverInterface;
use Pandora3\Contracts\RouterInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\ResponseInterface;
use Pandora3\Router\Exceptions\NoMatchedRouteException;

/**
 * Class Router
 * @package Pandora3\Router
 */
class Router implements RouterInterface, RequestHandlerInterface {

	use HasRoutes;

	/** @var Route[] */
	protected $routesMap = [];

	/** @var RouteResolverInterface */
	protected $routeResolver;
	
	/** @var mixed */
	protected $pageNotFoundHandler;
	
	public const METHOD_GET = 'get';
	public const METHOD_POST = 'post';
	public const METHOD_PUT = 'put';
	public const METHOD_DELETE = 'delete';
	public const METHOD_PATCH = 'patch';
	public const METHOD_HEAD = 'head';
	public const METHOD_OPTIONS = 'options';
	public const METHOD_ANY = 'any';
	
	/** @var array */
	protected static $methodFlags = [
		self::METHOD_GET => Route::METHOD_FLAG_GET,
		self::METHOD_POST => Route::METHOD_FLAG_POST,
		self::METHOD_PUT => Route::METHOD_FLAG_PUT,
		self::METHOD_DELETE => Route::METHOD_FLAG_DELETE,
		self::METHOD_PATCH => Route::METHOD_FLAG_PATCH,
		self::METHOD_HEAD => Route::METHOD_FLAG_HEAD,
		self::METHOD_OPTIONS => Route::METHOD_FLAG_OPTIONS,
	];
	
	/** @var array */
	protected static $patterns = [
		'any' => '(.*)',
		'all' => '([^/]+)',
		'id' => '(\d+)',
		'string' => '([\w\-_]+)',
	];

	/**
	 * Router constructor
	 * @param RouteResolverInterface $routeResolver
	 * @param mixed $pageNotFoundHandler
	 */
	public function __construct(RouteResolverInterface $routeResolver, $pageNotFoundHandler) {
		$this->routeResolver = $routeResolver;
		$this->pageNotFoundHandler = $pageNotFoundHandler;
	}

	/**
	 * @param string $alias
	 * @param string $pattern
	 */
	public static function registerPattern(string $alias, string $pattern): void {
		if (isset(self::$patterns[$alias])) {
			// todo: warning "Unable to redefine route pattern"
			return;
		}
		self::$patterns[$alias] = '('.$pattern.')';
	}

	/**
	 * @param ContainerInterface $container
	 * @param mixed $pageNotFoundHandler
	 */
	public static function use(ContainerInterface $container, $pageNotFoundHandler): void {
		$container->bind(RouterInterface::class, Router::class);
		$container->bind(RequestHandlerInterface::class, Router::class);
		$container->singleton(Router::class, function() use ($container, $pageNotFoundHandler) {
			return $container->build(Router::class, ['pageNotFoundHandler' => $pageNotFoundHandler]);
		});
	}

	/**
	 * @param array $params
	 * @param \Closure $callback
	 */
	public function group(array $params, \Closure $callback): void {
		$routeGroup = new RouteGroup($params);
		$callback($routeGroup);
		$routes = $routeGroup->getRoutes();
		foreach ($routes as $route) {
			$this->addRoute($route);
		}
	}
	
	/**
	 * @param string $path
	 * @return string
	 */
	public static function generatePattern(string $path): string {
		$pattern = preg_replace_callback('/\\\\{(\w+)\\\\}/', function($matches) use ($path) {
			$patternType = $matches[1];
			$pattern = self::$patterns[$patternType] ?? null;
			if (is_null($pattern)) {
				throw new \LogicException("Unsupported pattern in route '$path'");
			}
			return $pattern;
		}, preg_quote($path, '#'));
		return '#^'.$pattern.'$#';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function addRoute(Route $route): void {
		$this->routes[] = $route;
		if ($route->name) {
			$this->routesMap[$route->name] = $route;
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function createRoute(string $path, int $methodFlags, $handler, $name = ''): Route {
		$path = preg_replace('#^/?#', '/', $path);
		$pattern = self::generatePattern($path);
		return new Route($path, $pattern, $methodFlags, $handler, $name);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRoutePath(string $routeName, array $arguments = []): string {
		$route = $this->routesMap[$routeName] ?? null;
		if (!$route) {
			// todo: typed exception
			throw new \RuntimeException("Route not found '$routeName'");
		}
		$i = 0;
		$argumentCount = count($arguments);
		return preg_replace_callback('/{\w+}/', function() use ($arguments, $argumentCount, &$i) {
			/* if ($i >= $argumentCount) {
				// todo: warning "Too many arguments for route"
			} */
			$argument = $arguments[$i] ?? null;
			$i++;
			return $argument;
		}, $route->path);
	}

	/**
	 * @param string[] $methods
	 * @return int
	 */
	public static function methodsToRouteFlags(array $methods): int {
		$flags = Route::METHOD_FLAG_NONE;
		foreach ($methods as $method) {
			if ($method === self::METHOD_ANY) {
				$flag = Route::METHOD_FLAG_ANY;
			} else if ($methods === self::METHOD_GET) {
				$flag = Route::METHOD_FLAG_GET | Route::METHOD_FLAG_HEAD;
			} else {
				$flag = self::$methodFlags[$method] ?? null;
				if (is_null($flag)) {
					throw new \RuntimeException("Unsupported route method '$method'");
				}
			}
			$flags |= $flag;
		}
		return $flags;
	}

	/**
	 * @param Route $route
	 * @param int $methodFlag
	 * @param string $uri
	 * @param array $arguments
	 * @return bool
	 */
	protected function matchRoute(Route $route, int $methodFlag, string $uri, array &$arguments): bool {
		return (
			$this->matchMethod($route, $methodFlag) &&
			$this->matchPath($route, $uri, $arguments)
		);
	}

	/**
	 * @param Route $route
	 * @param string $path
	 * @param array $arguments
	 * @return bool
	 */
	protected function matchPath(Route $route, string $path, array &$arguments): bool {
		if (!preg_match($route->pattern, $path, $matches)) {
			return false;
		}
		$arguments = array_map('urldecode', array_slice($matches, 1));
		return true;
	}

	/**
	 * @param Route $route
	 * @param int $methodFlag
	 * @return bool
	 */
	protected function matchMethod(Route $route, int $methodFlag): bool {
		return ($route->methodFlags & $methodFlag) > 0;
	}
	
	/**
	 * @param RequestInterface $request
	 * @return ResponseInterface
	 * @throws NoMatchedRouteException
	 */
	public function handleRequest(RequestInterface $request): ResponseInterface {
		// todo: cache routes
		$uri = $request->getUri();
		if ($uri !== '/') {
			$uri = preg_replace('#/$#', '', $uri);
		}

		$method = $request->getMethod();
		$methodFlag = self::$methodFlags[strtolower($method)] ?? null;
		if (is_null($methodFlag)) {
			throw new \LogicException("Unsupported request method '$method'");
		}
		
		$arguments = [];
		$matchedRoute = null;
		foreach ($this->routes as $route) {
			if ($this->matchRoute($route, $methodFlag, $uri, $arguments)) {
				$matchedRoute = $route;
				break;
			}
		}

		if (!$matchedRoute) {
			if (is_null($this->pageNotFoundHandler)) {
				throw new NoMatchedRouteException($method, $uri);
			}
			$pageNotFoundHandler = $this->routeResolver->resolveRoute($this->pageNotFoundHandler);
			return $pageNotFoundHandler($request);
		}
		
		$handler = $this->routeResolver->resolveRoute($matchedRoute->handler, $matchedRoute->middlewares);
		$response = $handler($request, ...$arguments);
		if (!($response instanceof ResponseInterface)) {
			$routeHandler = "['{$matchedRoute->handler[0]}', '{$matchedRoute->handler[1]}']";
			throw new \LogicException("Route $routeHandler resolved closure must return ResponseInterface"); // todo: typed exception
		}
		return $response;
	}

}