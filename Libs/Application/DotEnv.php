<?php
namespace Pandora3\Application;

use Pandora3\EnvParser\EnvParser;

/**
 * Class DotEnv
 * @package Pandora3\Application
 */
class DotEnv {
	
	/**
	 * @param string $envFile
	 * @param string|null $cacheFile
	 * @return array
	 */
	public static function load(string $envFile, ?string $cacheFile = null): array {
		if (!is_file($envFile)) {
			throw new \RuntimeException("Env file doesn't exist '$envFile'");
		}
		if (is_file($cacheFile) && filemtime($envFile) <= filemtime($cacheFile)) {
			return require($cacheFile);
		}
		$env = (new EnvParser())->parse(file_get_contents($envFile));

		$path = dirname($cacheFile);
		if (!is_dir($path)) {
			if (!mkdir($path, 0755, true)) {
				throw new \RuntimeException("Failed to create path '$path'");
			}
		}

		file_put_contents($cacheFile . '.tmp', self::generateCache($env), LOCK_EX);
		unlink($cacheFile);
		rename($cacheFile . '.tmp', $cacheFile);

		return $env;
	}
	
	/**
	 * @param array $env
	 * @return string
	 */
	protected static function generateCache(array $env): string {
		$cache = "<?php\n";
		$cache .= "// This file was generated by DotEnv cache. Don't modify\n\n";
		$cache .= "return [\n";
		foreach ($env as $key => $value) {
			if (is_string($value)) {
				$value = '"'.str_replace(['"', "\n", "\t"], ['\\"', '\\n', '\\t'], $value).'"';
			}
			$cache .= "\t\"$key\" => $value,\n";
		}
		$cache .= "];\n";
		return $cache;
	}
	
}