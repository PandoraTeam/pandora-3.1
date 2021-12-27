<?php
namespace Pandora3\Contracts;

/**
 * Interface RouteResolverInterface
 * @package Pandora3\Contracts
 */
interface RouteResolverInterface {
	
	/**
	 * @param mixed $handler
	 * @param array $middlewares
	 * @return \Closure
	 */
	function resolveRoute($handler, array $middlewares = []): \Closure;

}