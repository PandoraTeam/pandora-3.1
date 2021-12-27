<?php
namespace Pandora3\Application\Middlewares;

use Pandora3\Application\BaseApplication;
use Pandora3\Application\FlashMessages;
use Pandora3\Contracts\ApplicationExceptionInterface;
use Pandora3\Contracts\ApplicationLogicExceptionInterface;
use Pandora3\Contracts\MiddlewareInterface;
use Pandora3\Contracts\RedirectorInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\ResponseInterface;

/**
 * Class ApplicationExceptionMiddleware
 * @package Pandora3\Application\Middlewares
 */
class ApplicationExceptionMiddleware implements MiddlewareInterface {

	/** @var RedirectorInterface */
	protected $redirector;

	/** @var FlashMessages */
	protected $messages;
	
	/** @var string */
	protected $environment;

	/**
	 * ApplicationExceptionMiddleware constructor
	 * @param RedirectorInterface $redirector
	 * @param FlashMessages $messages
	 * @param string $environment
	 */
	public function __construct(RedirectorInterface $redirector, FlashMessages $messages, string $environment) {
		$this->redirector = $redirector;
		$this->messages = $messages;
		$this->environment = $environment;
	}

	/**
	 * @param RequestInterface $request
	 * @return string|null
	 */
	protected function getReferer(RequestInterface $request): ?string {
		$server = $request->server();
		$uriReferer = $server['HTTP_REFERER'] ?? null;
		$serverName = $server['SERVER_NAME'] ?? null;
		if (!$serverName) {
			// todo: log warning "Server name is missing"
			return null;
		}
		$host = parse_url($uriReferer, PHP_URL_HOST);
		if ($host !== $serverName) {
			return null;
		}
		if (!preg_match('#^(?:http:|https:|)?//[^/]+(.*)$#', $uriReferer, $matches)) {
			return null;
		}
		return $matches[1];
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function process(RequestInterface $request, \Closure $next, array $arguments): ResponseInterface {
		try {
			return $next($request, ...$arguments);
		} catch (ApplicationExceptionInterface $ex) {
			// throw all ApplicationExceptionInterface except ApplicationLogicExceptionInterface in dev and test modes
			if ($this->environment !== BaseApplication::ENV_PRODUCTION && !($ex instanceof ApplicationLogicExceptionInterface)) {
				throw $ex;
			}
			$this->messages->add(FlashMessages::Error, $ex->getMessage());
			$uriReferer = $this->getReferer($request);
			if (!$uriReferer) {
				// todo: log warning "Could not redirect, http referer is missing"
				throw $ex;
			}
			return $this->redirector->redirectUri($uriReferer);
		}
	}
	
}