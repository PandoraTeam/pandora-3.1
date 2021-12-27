<?php
namespace Pandora3\Application;

use Pandora3\Contracts\RedirectorInterface;
use Pandora3\Contracts\ResponseFactoryInterface;
use Pandora3\Contracts\ResponseInterface;
use Pandora3\Contracts\RouterInterface;
use Pandora3\Contracts\UriInterface;

/**
 * Class Redirector
 * @package Pandora3\Application
 */
class Redirector implements RedirectorInterface {

	/** @var ResponseFactoryInterface */
	protected $responseFactory;

	/** @var RouterInterface */
	protected $router;

	public function __construct(ResponseFactoryInterface $responseFactory, RouterInterface $router) {
		$this->responseFactory = $responseFactory;
		$this->router = $router;
	}

	/**
	 * {@inheritdoc}
	 */
	public function redirectUri($uri, int $code = 303): ResponseInterface {
		return $this->responseFactory->createResponse('', $code, ['Location' => $uri]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function redirect(string $routeName, array $arguments = [], int $code = 303): ResponseInterface {
		$uri = $this->router->getRoutePath($routeName, $arguments);
		return $this->redirectUri($uri, $code);
	}

}