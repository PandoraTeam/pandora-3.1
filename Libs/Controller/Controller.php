<?php
namespace Pandora3\Controller;

use Pandora3\Contracts\ControllerInterface;
use Pandora3\Contracts\RedirectorInterface;
use Pandora3\Contracts\RendererInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\ResponseFactoryInterface;
use Pandora3\Contracts\ResponseInterface;

/**
 * Class Controller
 * @package Pandora3\Controller
 */
abstract class Controller implements ControllerInterface {

	/** @var RequestInterface */
	protected $request;

	/** @var RendererInterface */
	protected $renderer;

	/** @var ResponseFactoryInterface */
	protected $responseFactory;

	/** @var RedirectorInterface */
	protected $redirector;

	/** @var string */
	protected $layout = 'Main';

	/** @var string */
	protected $_name;

	/**
	 * @param RendererInterface $renderer
	 * @param RedirectorInterface $redirector
	 * @param ResponseFactoryInterface $responseFactory
	 */
	public function __construct(
		RendererInterface $renderer, RedirectorInterface $redirector,
		ResponseFactoryInterface $responseFactory
	) {
		$this->renderer = $renderer;
		$this->redirector = $redirector;
		$this->responseFactory = $responseFactory;
	}
	
	/**
	 * @return array
	 */
	public function middlewares(): array {
		return [];
	}
	
	/**
	 * @param string $methodName
	 * @param RequestInterface $request
	 * @param array ...$arguments
	 * @return ResponseInterface
	 */
	public function handleAction(string $methodName, RequestInterface $request, ...$arguments): ResponseInterface {
		$this->request = $request;
		if (!method_exists($this, $methodName)) {
			$className = static::class;
			throw new \RuntimeException("Undefined controller method '$methodName' for [$className]");
		}
		return $this->$methodName(...$arguments);
	}

	/**
	 * @return array
	 */
	protected function getTemplateParams(): array {
		return [
			'layout' => 'Layout/' . $this->layout
		];
	}

	/**
	 * @return string
	 */
	protected function getName(): string {
		if (!$this->_name) {
			preg_match('/(.*\\\\)?(.*?)(Controller)?$/', static::class, $matches);
			$this->_name = $matches[2];
		}
		return $this->_name;
	}

	/**
	 * @return string
	 */
	protected function getViewPath(): string {
		return $this->getName();
	}

	/**
	 * @param string $layout
	 */
	protected function setLayout(string $layout): void {
		$this->layout = $layout;
	}

	/**
	 * @param string $view
	 * @param array $context
	 * @param int $code
	 * @return ResponseInterface
	 */
	protected function render(string $view, array $context = [], int $code = 200): ResponseInterface {
		$viewPath = $this->getViewPath() . '/' . $view;
		$context = array_replace($context, $this->getTemplateParams());
		try {
			$content = $this->renderer->render($viewPath, $context);
			return $this->responseFactory->createResponse($content, $code);
		} catch (\RuntimeException $ex) {
			$className = static::class;
			throw new \RuntimeException("Rendering view '$viewPath' failed for [$className]", E_WARNING, $ex);
		}
	}

	/**
	 * @param $uri
	 * @return ResponseInterface
	 */
	protected function redirectUri($uri): ResponseInterface {
		return $this->redirector->redirectUri($uri);
	}

	/**
	 * @param string $route
	 * @param array ...$arguments
	 * @return ResponseInterface
	 */
	protected function redirect(string $route, ...$arguments): ResponseInterface {
		return $this->redirector->redirect($route, $arguments);
	}

}