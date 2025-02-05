<?php
namespace Pandora3\Application;

use Pandora3\Container\Container;
use Pandora3\Contracts\ContainerInterface;
use Pandora3\Registry\Registry;

/**
 * Class BaseApplication
 * @package Pandora3\Application
 */
abstract class BaseApplication {

	use HasApplicationEnvironment;

	public const ENV_LOCAL = 'local';
	public const ENV_DEVELOPMENT = 'dev';
	public const ENV_PRODUCTION = 'prod';

	/** @var array */
	protected $env = [];

	/** @var Registry */
	protected $config;

	/** @var ContainerInterface */
	protected $container;

	/** @var static */
	protected static $instance;

	/**
	 * Application constructor
	 */
	public function __construct() {
		if (is_null(self::$instance)) {
			self::$instance = $this;
		}

		$this->env = DotEnv::load(ROOT.'/.env', ROOT.'/storage/cache/.env.cache.php'); // todo: avoid hardcoded storage path
		$this->environment = $this->getEnv('APP_ENV', self::ENV_DEVELOPMENT);

		$this->beforeInitConfig();
		$this->config = new Registry($this->loadConfig($this->getConfigPath()));

		$this->container = $this->createContainer();
		$this->dependencies($this->container);
		try {
			$this->init();
		} catch (\Throwable $error) {
			$this->handleException($error);
		}
	}
	
	/**
	 * @return string
	 */
	protected function getConfigPath(): string {
		return ROOT.'/config/config.php';
	}
	
	/**
	 * @return array
	 */
	public function __debugInfo() {
		$result = get_object_vars($this);
		$result['config'] = ['***']; // protect config values when dumping
		return $result;
	}

	/**
	 * Get application instance
	 * @return static
	 */
	public static function instance() {
		return self::$instance;
	}

	/**
	 * @return ContainerInterface
	 */
	protected function createContainer(): ContainerInterface {
		$container = new Container();
		$container->singleton(ContainerInterface::class, $container);
		return $container;
	}

	/**
	 * Initialize container dependencies
	 * @param ContainerInterface $container
	 */
	protected function dependencies(ContainerInterface $container): void { }

	/**
	 * Initialize application hook
	 */
	protected function init(): void { }
	
	/**
	 * Before init config hook
	 */
	protected function beforeInitConfig(): void { }

	/**
	 * Run application
	 */
	abstract public function run(): void;
	
	/**
	 * @param \Throwable $exception
	 * @throws \RuntimeException
	 */
	protected function handleException(\Throwable $exception): void {
		throw new \RuntimeException("Application error", E_ERROR, $exception);
	}


	/**
	 * Load config file
	 * @param string $configFile
	 * @return array
	 */
	protected function loadConfig(string $configFile): array {
		if (!is_file($configFile)) {
			throw new \RuntimeException('No config file');
		}
		return require($configFile);
	}

	/**
	 * Get env variable
	 * @param string $key
	 * @param mixed|null $default
	 * @return mixed|null
	 */
	public function getEnv(string $key, $default = null) {
		return $this->env[$key] ?? $default;
	}

	/**
	 * Get config variable
	 * @param string $key
	 * @param null $default
	 * @return mixed|null
	 */
	public function getConfig(string $key, $default = null) {
		return $this->config->get($key, $default);
	}

}