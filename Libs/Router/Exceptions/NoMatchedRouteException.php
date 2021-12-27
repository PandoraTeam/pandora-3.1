<?php
namespace Pandora3\Router\Exceptions;

/**
 * Class NoMatchedRouteException
 * @package Pandora3\Router\Exceptions
 */
class NoMatchedRouteException extends \Exception {
	
	/**
	 * NoMatchedRouteException constructor
	 * @param string $method
	 * @param string $uri
	 */
	public function __construct(string $method, string $uri) {
		parent::__construct("No matched route for: '$method' '$uri'", E_WARNING);
	}
	
}