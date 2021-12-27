<?php
namespace Pandora3\EnvParser;

/**
 * Class EnvParser
 * @package Pandora3\EnvParser
 */
class EnvParser {
	
	private const PATTERN_DOUBLE_QUOTE_STRING = '/^"((?:[^"\\\\]*(?:\\\\.)?)*)"$/';
	private const PATTERN_SINGLE_QUOTE_STRING = "/^'((?:[^'\\\\]*(?:\\\\.)?)*)'$/";
	
	protected static $constantValues = [
		'true' => true,
		'false' => false,
		'null' => null,
	];
	
	/**
	 * @param string $content
	 * @return array
	 */
	public function parse(string $content): array {
		$lines = $this->parseLines($content);
		$env = [];
		foreach ($lines as $line) {
			$line = trim($line);
			if (!$line || $line[0] === '#') {
				continue;
			}
			[$key, $value] = $this->parseLine($line) ?? ['', ''];
			if (!$key) {
				continue;
			}
			$env[$key] = $value;
		}
		return $env;
	}
	
	/**
	 * @param string $content
	 * @return string[]
	 */
	protected function parseLines(string $content): array {
		return explode("\n", str_replace("\r", "\n", $content));
	}
	
	/**
	 * @param string $line
	 * @return array|null
	 */
	protected function parseLine(string $line): ?array {
		$keyValue = explode('=', $line, 2);
		if (count($keyValue) < 2) {
			return null;
		}
		[$key, $value] = $keyValue;
		return [
			$this->parseKey($key),
			$this->parseValue($value)
		];
	}
	
	/**
	 * @param string $key
	 * @return string
	 */
	protected function parseKey(string $key): string {
		if (!preg_match('/^([a-zA-Z]\w*)\s*/', $key, $matches)) {
			throw new \RuntimeException("Wrong .env variable name: '$key'");
		}
		return $matches[1];
	}
	
	/**
	 * @param string $value
	 * @return string|bool|int|null
	 */
	protected function parseValue(string $value) {
		$value = trim($value);
		if ($value[0] === '"' || $value[0] === "'") {
			return $this->parseString($value);
		}
		$value = $this->trimComment($value);
		$loweredValue = strtolower($value);
		if (array_key_exists($loweredValue, self::$constantValues)) {
			return self::$constantValues[$loweredValue];
		}
		if (is_numeric($value)) {
			return $this->parseNumber($value);
		}
		if (preg_match('/\s/', $value)) {
			throw new \RuntimeException("Unquoted .env variable value: '$value'");
		}
		return $value;
	}
	
	/**
	 * @param string $value
	 * @return string
	 */
	protected function parseString(string $value): string {
		$quote = $value[0];
		$pattern = ($quote === '"')
			? self::PATTERN_DOUBLE_QUOTE_STRING
			: self::PATTERN_SINGLE_QUOTE_STRING;
		if (!preg_match($pattern, $value, $matches)) {
			throw new \RuntimeException("Non matching quotes in .env variable value: $value");
		}
		$value = str_replace(['\\'.$quote, '\\n', '\\t'], [$quote, "\n", "\t"], $matches[1]);
		return $value;
	}
	
	/**
	 * @param string $value
	 * @return string
	 */
	protected function trimComment(string $value): string {
		return preg_replace('/\s+#.*$/', '', $value);
	}
	
	/**
	 * @param string $value
	 * @return float|int
	 */
	protected function parseNumber(string $value) {
		if (strpos($value, '.') !== false) {
            return (float) $value;
        }
        return (int) $value;
	}

}