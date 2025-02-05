<?php
namespace Pandora3\Application\Middlewares;

use Pandora3\Contracts\ApplicationExceptionInterface;
use Pandora3\Contracts\MiddlewareInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\ResponseFactoryInterface;
use Pandora3\Contracts\ResponseInterface;

/**
 * Class ApplicationExceptionMiddleware
 * @package Pandora3\Application\Middlewares
 */
class AjaxResponseExceptionMiddleware implements MiddlewareInterface {
	
	/** @var ResponseFactoryInterface */
	protected $responseFactory;
	
	/**
	 * AjaxResponseExceptionMiddleware constructor
	 * @param ResponseFactoryInterface $responseFactory
	 */
	public function __construct(ResponseFactoryInterface $responseFactory) {
		$this->responseFactory = $responseFactory;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function process(RequestInterface $request, \Closure $next, array $arguments): ResponseInterface {
		if ($request->isAjax()) {
			try {
				return $next($request, ...$arguments);
			} catch (\Throwable $ex) {
				$message = ($ex instanceof ApplicationExceptionInterface)
					? $ex->getMessage()
					: 'Internal server error';
				return $this->responseFactory->createResponse("Error: {$message}", 500);
			}
		} else {
			return $next($request, ...$arguments);
		}
	}
	
}