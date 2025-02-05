<?php
namespace Pandora3\Cookie\Middlewares;

use Pandora3\Contracts\MiddlewareInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\ResponseInterface;
use Pandora3\Cookie\Cookies;

/**
 * Class SetCookiesMiddleware
 * @package Pandora3\Cookie\Middlewares
 */
class SetCookiesMiddleware implements MiddlewareInterface {

	/** @var Cookies $cookies */
	protected $cookies;

	public function __construct(Cookies $cookies) {
		$this->cookies = $cookies;
	}
	
	/**
	 * {@inheritdoc}
	 */
	function process(RequestInterface $request, \Closure $next, array $arguments): ResponseInterface {
		$response = $next($request, ...$arguments);
		foreach ($this->cookies->getCookies() as $cookie) {
			$response->setCookie($cookie);
		}
		return $response;
	}

}