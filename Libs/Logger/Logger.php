<?php
namespace Pandora3\Logger;

use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\LoggerInterface;
use Pandora3\Contracts\LogHandlerInterface;
use Pandora3\Logger\Handlers\StreamLogHandler;

/**
 * Class Logger
 * @package Pandora3\Logger
 */
class Logger implements LoggerInterface {

	public const Emergency	= 'emergency';
	public const Alert		= 'alert';
	public const Critical	= 'critical';
	public const Error		= 'error';
	public const Warning	= 'warning';
	public const Notice		= 'notice';
	public const Info		= 'info';
	public const Debug		= 'debug';
	
	protected const WarningLevel = 4;
	
	protected static $levelPriorities = [
		self::Emergency	=> 0,
		self::Alert		=> 1,
		self::Critical	=> 2,
		self::Error		=> 3,
		self::Warning	=> 4,
		self::Notice	=> 5,
		self::Info		=> 6,
		self::Debug		=> 7,
	];
	
	/** @var array */
	protected $handlers;
	
	/** @var array */
	protected $sharedContext;
	
	/**
	 * @param LogHandlerInterface[] $handlers
	 * @param array $sharedContext
	 */
	public function __construct(array $handlers, array $sharedContext = []) {
		$this->handlers = $handlers;
		$this->sharedContext = $sharedContext;
		foreach ($handlers as $handler) {
			$handlerLevel = $handler->getLevel();
			if (!isset(self::$levelPriorities[$handlerLevel])) {
				$handlerClassName = get_class($handler);
		 		$this->log(self::Warning, "Unexpected log handler [$handlerClassName] level: '{$handlerLevel}' (setting it to 'warning' level)");
			}
		}
	}
	
	/**
	 * @param ContainerInterface $container
	 * @param array $config
	 * @param array $sharedContext
	 */
	public static function use(ContainerInterface $container, array $config, array $sharedContext = []): void {
		$container->bind(LoggerInterface::class, Logger::class);
		$container->singleton(Logger::class, static function(ContainerInterface $container) use ($config, $sharedContext) {
			[$handlers, $errors] = self::createLogHandlers($container, $config);
			$logger = new Logger($handlers, $sharedContext);
			foreach ($errors as $error) {
				$logger->log($error->level, $error->message, $error->context);
			}
			return $logger;
		});
		StreamLogHandler::use($container);
	}
	
	/**
	 * @param array $context
	 */
	public function setContext(array $context): void {
		$this->sharedContext = $context;
	}
	
	/**
	 * @param ContainerInterface $container
	 * @param array $config
	 * @return LogHandlerInterface[]
	 */
	protected static function createLogHandlers(ContainerInterface $container, array $config): array {
		$errors = [];
		$channel = $config['channel'] ?? null;
		if (is_null($channel)) {
			throw new \RuntimeException("Logging channel is missing in configuration file");
		}
		$handlers = [];
		if (!isset($config[$channel])) {
			throw new \RuntimeException("Logging channel named '{$channel}' is missing in configuration file");
		}
		foreach ($config[$channel] ?? [] as $handler) {
			$handlerConfig = $config['handlers'][$handler] ?? null;
			if (!$handlerConfig) {
				$errors[] = (object) [
					'level' => self::Error,
					'message' => "Logging handler named '{$handler}' is missing in configuration file",
					'context' => [],
				];
				continue;
			}
			try {
				$handlers[] = self::createLogHandler($container, $handlerConfig);
			} catch (\Throwable $error) {
				$className = $handlerConfig['driver'];
				$exception = new \RuntimeException("Error while instantiating [$className] log handler", E_ERROR, $error);
				$errors[] = (object) [
					'level' => self::Error,
					'message' => $exception->getMessage(),
					'context' => ['exception' => $exception],
				];
			}
		}
		return [$handlers, $errors];
	}
	
	/**
	 * @param ContainerInterface $container
	 * @param array $config
	 * @return LogHandlerInterface
	 */
	protected static function createLogHandler(ContainerInterface $container, array $config): LogHandlerInterface {
		$className = $config['driver'];
		$filter = $config['filter'] ?? null;
		unset($config['driver']);
		unset($config['filter']);
		
		if (!array_key_exists(LogHandlerInterface::class, class_implements($className))) {
			throw new \RuntimeException("Logging handler [$className] should implement LogHandlerInterface");
		}
		$handler = $container->make($className, $config);
		if ($filter) {
			[$filterClassName, $filterConfig] = $filter;
			if (!array_key_exists(LogHandlerInterface::class, class_implements($filterClassName))) {
				throw new \RuntimeException("Logging filter [$filterClassName] should implement LogHandlerInterface");
			}
			return $container->make($filterClassName, [
				'handler' => $handler,
				'config' => $filterConfig
			]);
		}
		return $handler;
	}
	
	/**
	 * @param string $level
	 * @return int|null
	 */
	public static function getLevelPriority(string $level): ?int {
		return self::$levelPriorities[$level] ?? null;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function emergency(string $message, array $context = []): void {
		$this->logRecord(self::Emergency, $message, $context);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function alert(string $message, array $context = []): void {
		$this->logRecord(self::Alert, $message, $context);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function critical(string $message, array $context = []): void {
		$this->logRecord(self::Critical, $message, $context);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function error(string $message, array $context = []): void {
		$this->logRecord(self::Error, $message, $context);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function warning(string $message, array $context = []): void {
		$this->logRecord(self::Warning, $message, $context);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function notice(string $message, array $context = []): void {
		$this->logRecord(self::Notice, $message, $context);
	}
	
	/**
	 * Interesting events. User logs in, SQL logs
	 *
	 * @param string $message
	 * @param array $context
	 * @param string|null $channel
	 */
	public function info(string $message, array $context = [], ?string $channel = null): void {
		$this->logRecord(self::Info, $message, $context, $channel);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function debug(string $message, array $context = []): void {
		$this->logRecord(self::Debug, $message, $context);
	}
	
	/**
	 * Logs with an arbitrary level
	 *
	 * @param string $level
	 * @param string $message
	 * @param array $context
	 * @param string|null $channel
	 */
	public function log(string $level, string $message, array $context = [], ?string $channel = null): void {
		$this->logRecord($level, $message, $context, $channel);
	}
	
	/**
	 * @param string $level
	 * @param string $message
	 * @param array $context
	 * @param null|string $channel
	 */
	protected function logRecord(string $level, string $message, array $context, ?string $channel = null) {
		$priority = self::getLevelPriority($level);
		if (is_null($priority)) {
			throw new \InvalidArgumentException("Unexpected log level: '$level'");
		}
		if ($this->sharedContext) {
			$context = array_replace($context, $this->sharedContext);
		}
		// todo: think if need to add 'time'
		$record = (object) [
			'level' => $level,
			'message' => $message,
			'context' => $context,
			'channel' => $channel,
		];
		foreach ($this->handlers as $handler) {
			$handlerPriority = self::getLevelPriority($handler->getLevel()) ?? self::WarningLevel;
			if ($priority <= $handlerPriority) {
				$handler->handle($record);
			}
		}
	}
	
}