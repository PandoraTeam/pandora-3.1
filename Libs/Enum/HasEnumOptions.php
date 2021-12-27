<?php
namespace Pandora3\Enum;

/**
 * Trait HasEnumOptions
 */
trait HasEnumOptions {
	
	/**
	 * @return array
	 */
	public static function getOptions(): array {
		return array_filter(self::$titles, function($key) {
			return $key !== self::None;
		}, ARRAY_FILTER_USE_KEY);
	}
	
	/**
	 * @param array $only
	 * @return array
	 */
	public static function getOptionsOnly(array $only = []): array {
		return array_filter(self::$titles, function($key) use ($only) {
			return in_array($key, $only, true);
		}, ARRAY_FILTER_USE_KEY);
	}
	
	/**
	 * @param array $exclude
	 * @return array
	 */
	public static function getOptionsExclude(array $exclude = []): array {
		return array_filter(self::$titles, function($key) use ($exclude) {
			return $key !== self::None && !in_array($key, $exclude, true);
		}, ARRAY_FILTER_USE_KEY);
	}
	
	/**
	 * @return array
	 */
	public static function getConstants(): array {
		try {
			$reflection = new \ReflectionClass(static::class);
			return $reflection->getConstants();
		} catch (\ReflectionException $ex) {
			$className = static::class;
			throw new \RuntimeException("Unable to create ReflectionClass for [$className]", E_ERROR, $ex);
		}
	}
	
	/**
	 * @param $value
	 * @return string
	 */
	public static function getConstantName($value): string {
		$constants = self::getConstants();
		foreach ($constants as $name => $constantValue) {
			if ($constantValue === $value) {
				return $name;
			}
		}
		return '';
	}

}