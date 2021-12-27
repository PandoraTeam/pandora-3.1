<?php
namespace Pandora3\Application\Middlewares;

use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\MiddlewareInterface;
use Pandora3\Contracts\RedirectorInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\ResponseInterface;
use Pandora3\Contracts\SessionInterface;
use Pandora3\Contracts\ValidationExceptionInterface;
use Pandora3\Http\Request;

/**
 * Class ValidationExceptionMiddleware
 * @package Pandora3\Application\Middlewares
 */
class ValidationExceptionMiddleware implements MiddlewareInterface {

	/** @var ContainerInterface */
	protected $container;
	
	/** @var RedirectorInterface */
	protected $redirector;

	/** @var SessionInterface */
	protected $session;
	
	/**
	 * ApplicationExceptionMiddleware constructor
	 * @param ContainerInterface $container
	 * @param RedirectorInterface $redirector
	 * @param SessionInterface $session
	 */
	public function __construct(ContainerInterface $container, RedirectorInterface $redirector, SessionInterface $session) {
		$this->container = $container;
		$this->redirector = $redirector;
		$this->session = $session;
	}

	/**
	 * @param RequestInterface $request
	 * @return RequestInterface
	 */
	protected function prepareRequest(RequestInterface $request): RequestInterface {
		$validationData = $this->session->get('_validationFailed');
		$this->session->remove('_validationFailed');
		if (!$validationData || $request->getUri() !== $validationData['uri']) {
			return $request;
		}
		$post = array_replace($request->postValues(), $validationData['post']);
		$attributes = array_replace($request->attributes(), ['validationMessages' => $validationData['messages']]);
		return $this->container->make(RequestInterface::class, [ // todo: probably use factory or withPost()
			'method' => $request->getMethod(), 'uri' => $request->getUri(),
			'get' => $request->getValues(), 'post' => $post,
			'attributes' => $attributes, 'cookies' => $request->cookies(),
			'files' => $request->files(), 'server' => $request->server(),
		]);
	}
	
	/**
	 * @param RequestInterface $request
	 * @return string|null
	 */
	protected function getReferer(RequestInterface $request): ?string {
		$server = $request->server();
		$uriReferer = $server['HTTP_REFERER'] ?? null;
		$serverName = $server['SERVER_NAME'] ?? null;
		dump($server);
		if (!$serverName) {
			// todo: log warning "Server name is missing"
			return null;
		}
		$host = parse_url($uriReferer, PHP_URL_HOST);
		if (
			$host !== $serverName ||
			!preg_match('#^(?:http:|https:|)?//[^/]+(.*)$#', $uriReferer, $matches)
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
			$request = $this->prepareRequest($request);
			return $next($request, ...$arguments);
		} catch (ValidationExceptionInterface $ex) {
			$uriReferer = $this->getReferer($request);
			$this->session->set('_validationFailed', [
				'post' => $request->all(Request::METHOD_POST),
				'messages' => $ex->getMessages(),
				'uri' => $uriReferer,
			]);
			if (!$uriReferer) {
				// todo: log warning "Could not redirect, http referer is missing"
				header("HTTP/1.1 422 Unprocessable Entity", true, 422);
				throw $ex;
			}
			return $this->redirector->redirectUri($uriReferer);
		}
	}
	
}