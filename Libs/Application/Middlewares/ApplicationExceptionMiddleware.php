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
	
	/** @var string */
	protected $baseUri;

	/**
	 * ApplicationExceptionMiddleware constructor
	 * @param RedirectorInterface $redirector
	 * @param FlashMessages $messages
	 * @param string $environment
	 * @param string $baseUri
	 */
	public function __construct(RedirectorInterface $redirector, FlashMessages $messages, string $environment, string $baseUri) {
		$this->redirector = $redirector;
		$this->messages = $messages;
		$this->environment = $environment;
		$this->baseUri = $baseUri;
	}

	/**
	 * @param RequestInterface $request
	 * @return string|null
	 */
	protected function getReferer(RequestInterface $request): ?string {
		// todo: duplicate with app/App.php
		// todo: possibly only allow get request
		$server = $request->server();
		$uriReferer = $server['HTTP_REFERER'] ?? null;
		$serverName = $server['SERVER_NAME'] ?? null;
		if (!$serverName) {
			trigger_error('Server name is missing', E_USER_WARNING);
			return null;
		}
		$host = parse_url($uriReferer, PHP_URL_HOST);
		if (
			$host !== $serverName ||
			// !preg_match('#^(?:http:|https:|)//[^/]+([^?]*)#', $uriReferer, $matches)
			!preg_match('#^(?:http:|https:|)//[^/]+(.*)#', $uriReferer, $matches)
		) {
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
			// ApplicationLogicExceptionInterface always redirect with error flash message
			// ApplicationExceptionInterface:
			// 		DEV & TEST: throw exception (dump to browser)
			// 		PRODUCTION: redirect with error flash message
			
			if ($this->environment !== BaseApplication::ENV_PRODUCTION && !($ex instanceof ApplicationLogicExceptionInterface)) {
				throw $ex;
			}
			$this->messages->add(FlashMessages::Error, $ex->getMessage());
			$redirectUri = $this->getReferer($request) ?? null;
			if (is_null($redirectUri) || ($redirectUri === $request->getUri() && !$request->isPost())) {
				$redirectUri = $this->baseUri;
			}
			/* if (!$uriReferer) {
				// to do: log warning "Could not redirect, http referer is missing"
				echo "Could not redirect, http referer is missing<br>";
				throw $ex;
			} */
			return $this->redirector->redirectUri($redirectUri);
		}
	}
	
}