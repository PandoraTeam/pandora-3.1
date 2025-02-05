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
use Pandora3\Http\Response;

/**
 * Class ValidationExceptionMiddleware
 * @package Pandora3\Application\Middlewares
 */
class ValidationExceptionMiddleware implements MiddlewareInterface {

	protected const SessionKeyValidationData = '_validationFailed';

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
		$validationData = $this->session->get(self::SessionKeyValidationData);
		$this->session->remove(self::SessionKeyValidationData);
		if (!$validationData || $request->getUri() !== $validationData['uri']) {
			return $request;
		}
		$post = array_replace($request->postValues(), $validationData['post']);
		$attributes = array_replace($request->attributes(), [
			'validationMessages' => $validationData['messages'],
			'validationForm' => $validationData['formName'] ?? null,
		]);
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
			$request = $this->prepareRequest($request);
			return $next($request, ...$arguments);
		} catch (ValidationExceptionInterface $ex) {
			$uriReferer = $this->getReferer($request);
			$this->session->set(self::SessionKeyValidationData, [
				'post' => $request->all(Request::METHOD_POST),
				'messages' => $ex->getMessages(),
				'formName' => $ex->getFormName(),
				'uri' => $uriReferer,
			]);
			if (!$uriReferer) {
				trigger_error('Could not redirect. Referer uri is missing', E_USER_WARNING);
				// header("HTTP/1.1 422 Unprocessable Entity", true, Response::CODE_UNPROCESSABLE_ENTITY);
				(new Response('', Response::CODE_UNPROCESSABLE_ENTITY))->send();
				throw $ex;
			}
			return $this->redirector->redirectUri($uriReferer);
		}
	}
	
}