<?php
namespace Pandora3\Authentication\Middlewares;

use Pandora3\Authentication\Authentication;
use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\MiddlewareInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\ResponseInterface;

/**
 * Class AuthenticationMiddleware
 * @package Pandora3\Authentication\Middlewares
 */
class AuthenticationMiddleware implements MiddlewareInterface {
	
	/** @var Authentication */
	protected $authentication;
	
	/** @var \Closure */
	protected $forbiddenHandler;
	
	/**
	 * AuthenticationMiddleware constructor.
	 * @param Authentication $authentication
	 * @param \Closure $forbiddenHandler
	 */
	public function __construct(Authentication $authentication, \Closure $forbiddenHandler) {
		$this->authentication = $authentication;
		$this->forbiddenHandler = $forbiddenHandler;
	}
	
	/**
	 * @param ContainerInterface $container
	 * @param \Closure $forbiddenHandler
	 */
	public static function use(ContainerInterface $container, \Closure $forbiddenHandler) {
		$container->bind(AuthenticationMiddleware::class, function() use ($container, $forbiddenHandler) {
			return $container->build(AuthenticationMiddleware::class, ['forbiddenHandler' => $forbiddenHandler]);
		});
		$container->singleton(AuthenticationMiddleware::class);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function process(RequestInterface $request, \Closure $next, array $arguments): ResponseInterface {
		if (!$this->authentication->getUser()) {
			$forbiddenHandler = $this->forbiddenHandler;
			return $forbiddenHandler($request, ...$arguments);
		}
		return $next($request, ...$arguments);
	}

}