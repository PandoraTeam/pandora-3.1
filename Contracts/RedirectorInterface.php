<?php
namespace Pandora3\Contracts;

/**
 * Interface RedirectorInterface
 * @package Pandora3\Contracts
 */
interface RedirectorInterface {

	/**
	 * @param string|UriInterface $uri
	 * @param int $code
	 * @return ResponseInterface
	 */
	function redirectUri($uri, int $code = 303): ResponseInterface;

	/**
	 * @param string $route
	 * @param array $arguments
	 * @param int $code
	 * @return ResponseInterface
	 */
	function redirect(string $route, array $arguments = [], int $code = 303): ResponseInterface;

}