<?php
namespace Pandora3\Application\Middlewares;

use Pandora3\Contracts\MiddlewareInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\ResponseInterface;
use Pandora3\Contracts\SessionInterface;

/**
 * Class SaveSessionMiddleware
 * @package Pandora3\Application\Middlewares
 */
class SaveSessionMiddleware implements MiddlewareInterface {

	/** @var SessionInterface */
	protected $session;
	
	/**
	 * SaveSessionMiddleware constructor
	 * @param SessionInterface $session
	 */
	public function __construct(SessionInterface $session) {
		$this->session = $session;
	}
	
	/**
	 * {@inheritdoc}
	 */
	function process(RequestInterface $request, \Closure $next, array $arguments): ResponseInterface {
		$response = $next($request, ...$arguments);
		$this->session->save();
		return $response;
	}
	
}