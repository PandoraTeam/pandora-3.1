<?php
namespace Pandora3\Application;

use Pandora3\Application\Middlewares\ApplicationExceptionMiddleware;
use Pandora3\Application\Routing\RouteResolver;
use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\RedirectorInterface;
use Pandora3\Contracts\RequestFactoryInterface;
use Pandora3\Contracts\RequestHandlerInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\RouteResolverInterface;
use Pandora3\Contracts\RouterInterface;
use Pandora3\Events\Dispatcher;

/**
 * Pandora manifest
 *
 * Goals:
 * 1. Security
 * 2. Convenience (in development)
 * 3. Reusability
 * 4. Performance
 *
 * I believe that Security, Reusability and Performance can be achieved (and should) without compromising of Convenience in development.
 *
 * Achieved by:
 * 1. Minimalistic (Lightweight)
 *        Provide basic implementations with as few code as possible. They can be extended or replaced, that final set of functionality will be sufficient and convenient for project development.
 *        Ensure basic functionality or architecture does not restrict the implementation of desired behaviors that might be needed for wast variety of projects. (most tricky thing?)
 * 2. Modular
 *        Avoid unnecessary dependencies. Keeping things modular to reduce the amount of unused code in final project setup. Also this will reduce a chance of dependencies version conflict.
 *        Inversion of dependency. Use interfaces and Container to avoid direct dependency on specific implementation.
 *
 * Good practices:
 * 1. Default case out of the box
 *        Ready to use solutions for simple cases without need of any configuration.
 *        Provide default set of parameters (whenever it makes sense) rather than explicitly require for them. With ability to override them.
 * 2. Everything is good in moderation
 *        Lower requirements for one goal if it will significantly reduce quality of other goals.
 */
 
 /**
 * Манифест Pandora
 *
 * Цели:
 * 1. Безопасность
 * 2. Удобвство (разработки)
 * 3. Повторное использование
 * 4. Производительсноть
 *
 * Я верю что Безопасность, Повторное использование и Производительность могут быть достигнуты (и должны) без потери Удобства разработки.
 *
 * Достигается за счет:
 * 1. Минималистичность (Легковесность)
 *        Предоставьте базовую функиональность за счет минимального количеством кода. Она может быть расширена или заменена, чтобы итоговый набор функциональности был достаточным и удобным для разработки проекта.
 *        Удостоверьтесь что базовая функциональность или выбранная архитектура не ограничивает реализацию желаемого поведения которое может потребоваться для широкого спектра проектов. (самая нетривиальная вещь?)
 * 2. Модульность
 *        Борьба с избыточными зависимостями. Поддерживайте модульность чтобы снизить количество неиспользуемого кода в итоговом проекте. Также это снизит шанс конфликтов версий зависимостей.
 *        Инверсия зависимости. Используйте интерфейсы и Container (контейнер) чтобы избавиться от прямой зависимости на реализацию.
 *
 * Хорошие практики:
 * 1. Поведение по умолчанию из коробки
 *        Решения готовые к использованию для простых случаев без дополнительной настройки.
 *        Предоставьте набор параметров по умолчанию (там где это имеет смысл) вместо требования их явного указания. С возможностью переопределить их.
 * 2. Все хорошо в меру
 *        Снижайте требования для достижения одной из целей если это существенно снижает качесвто достижения других целей.
 */


/**
 * Class Application
 * @package Pandora3\Application
 */
abstract class Application extends BaseApplication {

	/** @var RedirectorInterface */
	protected $redirector;

	/** @var RouterInterface */
	protected $router;

	/** @var RequestHandlerInterface */
	protected $requestHandler;

	/**
	 * {@inheritdoc}
	 */
	protected function dependencies(ContainerInterface $container): void {
		$container->bind(RedirectorInterface::class, Redirector::class);
		$container->singleton(Redirector::class);
		$container->singleton(Dispatcher::class);

		// todo: extract FlashMessages to separate package
		// FlashMessages requires Events package
		$container->singleton(FlashMessages::class);

		$container->bind(RouteResolverInterface::class, RouteResolver::class);

		$environment = $this->getEnvironment();
		$baseUri = $this->getBaseUri();
		$container->bind(ApplicationExceptionMiddleware::class,
			static function(ContainerInterface $container) use ($environment, $baseUri) {
				return $container->build(ApplicationExceptionMiddleware::class, [
					'environment' => $environment,
					'baseUri' => $baseUri,
				]);
			}
		);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function init(): void {
		$this->redirector = $this->container->make(RedirectorInterface::class);
		$this->router = $this->container->make(RouterInterface::class);
		$this->requestHandler = $this->container->make(RequestHandlerInterface::class);
	}

	/**
	 * @return ContainerInterface
	 */
	public function getContainer(): ContainerInterface {
		return $this->container;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function run(): void {
		$request = null;
		try {
			/** @var RequestFactoryInterface $requestFactory */
			$requestFactory = $this->container->make(RequestFactoryInterface::class);
			$request = $requestFactory->createRequest();
			$response = $this->requestHandler->handleRequest($request);
			$response->send();
		} catch (\Throwable $error) {
			$this->handleException($error, $request);
		}
	}
	
	/**
	 * @param \Throwable $exception
	 * @param RequestInterface|null $request
	 */
	protected function handleException(\Throwable $exception, ?RequestInterface $request = null): void {
		parent::handleException($exception);
	}
	
	/**
	 * @return string
	 */
	public function getSecret(): string {
		return $this->config->get('secret');
	}
	
	/**
	 * @return string
	 */
	public function getBaseUri(): string {
		return $this->config->get('baseUri', '/');
	}
	
	/**
	 * @param string $routeName
	 * @param array $arguments
	 * @return string
	 */
	public function getRoutePath(string $routeName, array $arguments = []): string {
		return $this->router->getRoutePath($routeName, $arguments);
	}

}