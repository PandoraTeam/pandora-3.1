<?php
namespace Pandora3\Contracts;

/**
 * Interface MiddlewareInterface
 * @package Pandora3\Contracts
 */
interface MiddlewareInterface {
	
	/**
	 * @param RequestInterface $request
	 * @param \Closure $next
	 * @param array $arguments
	 * @return ResponseInterface
	 */
	function process(RequestInterface $request, \Closure $next, array $arguments): ResponseInterface;

}