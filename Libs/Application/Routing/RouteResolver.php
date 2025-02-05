<?php
namespace Pandora3\Application\Routing;

use Pandora3\Application\Interfaces\MiddlewareGroupInterface;
use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\ControllerInterface;
use Pandora3\Contracts\MiddlewareInterface;
use Pandora3\Contracts\RouteResolverInterface;

/**
 * Class RouteResolver
 * @package Pandora3\Application\Routing
 */
class RouteResolver implements RouteResolverInterface {

	/** @var ContainerInterface */
	protected $container;

	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}

	/**
	 * @param \Closure|array|string $handler
	 * @param array $middlewares
	 * @return \Closure
	 */
	public function resolveRoute($handler, array $middlewares = []): \Closure {
		$handler = $this->resolveRouteHandler($handler, $middlewares);
		$middlewares = $this->resolveMiddlewares($middlewares);
		return $this->chainMiddlewares($handler, $middlewares);
	}
	
	/**
	 * @param \Closure|array|string $handler
	 * @param array $middlewares
	 * @return \Closure
	 */
	protected function resolveRouteHandler($handler, array &$middlewares): \Closure {
		if ($handler instanceof \Closure) {
			return $handler;
		}
		if (is_string($handler) && is_callable($handler)) {
			return \Closure::fromCallable($handler);
		}
		
		[$controller, $methodName] = $handler;
		if (is_string($controller)) {
			$controller = $this->container->make($controller);
		}
		
		if (!($controller instanceof ControllerInterface)) {
			return \Closure::fromCallable([$controller, $methodName]);
		}
		
		$middlewares = array_merge($middlewares, $controller->middlewares());

		return static function($request, ...$arguments) use ($controller, $methodName) {
			return $controller->handleAction($methodName, $request, ...$arguments);
		};
	}
	
	/**
	 * @param array $middlewares
	 * @return MiddlewareInterface[]
	 */
	protected function resolveMiddlewares(array $middlewares): array {
		$result = [];
		foreach ($middlewares as $middleware) {
			if ($this->isMiddlewareGroup($middleware)) {
				/** @var MiddlewareGroupInterface $middlewareGroup */
				$middlewareGroup = $this->resolveInstance($middleware);
				$result = array_merge($result, $middlewareGroup->getMiddlewares()); // todo: probably handle MiddlewareGroup-s inside MiddlewareGroup
			} else {
				$result[] = $middleware;
			}
		}
		$result = array_unique($result);
		foreach ($result as $i => $middleware) {
			$middleware = $this->resolveInstance($middleware);
			if (!($middleware instanceof MiddlewareInterface)) {
				$className = get_class($middleware);
				throw new \RuntimeException("Middleware [$className] should implement MiddlewareInterface");
			}
			$result[$i] = $middleware;
		}
		return $result;
	}
	
	/**
	 * @param object|string $middleware
	 * @return bool
	 */
	protected function isMiddlewareGroup($middleware): bool {
		return array_key_exists(MiddlewareGroupInterface::class, class_implements($middleware));
	}
	
	/**
	 * @param object|string $middleware
	 * @return object
	 */
	protected function resolveInstance($middleware): object {
		if (is_string($middleware)) {
			return $this->container->make($middleware);
		}
		return $middleware;
	}
	
	/**
	 * @param \Closure $handler
	 * @param MiddlewareInterface[] $middlewares
	 * @return \Closure
	 */
	protected function chainMiddlewares(\Closure $handler, array $middlewares): \Closure {
		/** @var MiddlewareInterface[] $middlewares */
		$middlewares = array_reverse($middlewares);
		foreach ($middlewares as $middleware) {
			$handler = static function($request, ...$arguments) use ($middleware, $handler) {
				return $middleware->process($request, $handler, $arguments);
			};
		};
		return $handler;
	}
	
}