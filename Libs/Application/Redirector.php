<?php
namespace Pandora3\Application;

use Pandora3\Application\Events\RedirectingEvent;
use Pandora3\Contracts\DispatcherInterface;
use Pandora3\Contracts\LoggerInterface;
use Pandora3\Contracts\RedirectorInterface;
use Pandora3\Contracts\ResponseFactoryInterface;
use Pandora3\Contracts\ResponseInterface;
use Pandora3\Contracts\RouterInterface;
use Pandora3\Http\Response;

/**
 * Class Redirector
 * @package Pandora3\Application
 */
class Redirector implements RedirectorInterface {

	public const LogChannel = 'Redirect';

	/** @var ResponseFactoryInterface */
	protected $responseFactory;

	/** @var RouterInterface */
	protected $router;
	
	/** @var LoggerInterface */
	protected $logger;
	
	/** @var DispatcherInterface */
	protected $dispatcher;
	
	/**
	 * Redirector constructor
	 * @param ResponseFactoryInterface $responseFactory
	 * @param RouterInterface $router
	 * @param LoggerInterface $logger
	 * @param DispatcherInterface $dispatcher
	 */
	public function __construct(
		ResponseFactoryInterface $responseFactory, RouterInterface $router,
		LoggerInterface $logger, DispatcherInterface $dispatcher
	) {
		$this->responseFactory = $responseFactory;
		$this->router = $router;
		$this->logger = $logger;
		$this->dispatcher = $dispatcher;
	}

	/**
	 * {@inheritdoc}
	 */
	public function redirectUri($uri, int $code = Response::CODE_SEE_OTHER): ResponseInterface {
		$this->logger->info('Redirect', [
			'uri' => $uri,
			'code' => $code
		], self::LogChannel);
		$this->dispatcher->dispatch(new RedirectingEvent($uri, $code));
		return $this->responseFactory->createResponse('', $code, ['Location' => $uri]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function redirect(string $routeName, array $arguments = [], int $code = Response::CODE_SEE_OTHER): ResponseInterface {
		$uri = $this->router->getRoutePath($routeName, $arguments);
		return $this->redirectUri($uri, $code);
	}

}