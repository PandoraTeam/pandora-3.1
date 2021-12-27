<?php
namespace Pandora3\Application;

/**
 * Trait HasApplicationEnvironment
 * @package Pandora3\Application
 */
trait HasApplicationEnvironment {

	/** @var string */
	protected $environment;

	/**
	 * @return string
	 */
	public function getEnvironment(): string {
		return $this->environment;
	}

	/**
	 * @return bool
	 */
	public function isLocal(): bool {
		return $this->environment === self::ENV_LOCAL;
	}

	/**
	 * @return bool
	 */
	public function isDevelopment(): bool {
		return $this->environment === self::ENV_DEVELOPMENT;
	}

	/**
	 * @return bool
	 */
	public function isProduction(): bool {
		return $this->environment === self::ENV_PRODUCTION;
	}

}