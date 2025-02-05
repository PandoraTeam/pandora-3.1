<?php
namespace Pandora3\File;

use Pandora3\Contracts\StreamInterface;

/**
 * Class Stream
 * @package Pandora3\Logger\Stream
 */
class FileStream implements StreamInterface {

	public const MAX_CHUNK_SIZE = 2147483647; // Max 32 int

	public const MIN_CHUNK_SIZE = 10 * 1024;

	public const DEFAULT_CHUNK_SIZE = 10 * 1024 * 1024;

	/** @var string */
	protected $path;
	
	/** @var resource */
	protected $resource = null;

	/**
	 * FileStream constructor
	 * @param string $path
	 */
	public function __construct(string $path) {
		if (!$this->validatePath($path)) {
			throw new \InvalidArgumentException("Invalid stream path: '$path'");
		}
		$this->path = $path;
	}

	/**
	 * FileStream destructor
	 */
	public function __destruct() {
		if (is_resource($this->resource)) {
			fclose($this->resource);
			$this->resource = null;
		}
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	protected static function validatePath(string $path): bool {
		if (strpos($path, '://') === false) {
			return true;
		}
		return in_array($path, [
			'php://stdout',
			'php://stderr'
		]);
	}

	/**
	 * @param string|false $memoryLimit
	 * @return int|null
	 */
	protected static function parseMemoryLimit($memoryLimit): ?int {
		if (!is_string($memoryLimit)) {
			return null;
		}
		$memoryLimit = trim($memoryLimit);
		$bytes = (int) $memoryLimit;
		if ($bytes < 0) {
			return null;
		}
		$unit = strtolower(substr($memoryLimit, -1));
		switch ($unit) {
			case 'g':
				$bytes *= 1024 * 1024 * 1024;
				break;
			case 'm':
				$bytes *= 1024 * 1024;
				break;
			case 'k':
				$bytes *= 1024;
				break;
			default:
				break;
		}
		return $bytes;
	}

	/**
	 * Initialize resource
	 */
	protected function initialize(): void {
		FileUtils::createPath(dirname($this->path));
		$resource = fopen($this->path, 'a');
		if (!is_resource($resource)) {
			throw new \RuntimeException("Failed to open file for writing: '$this->path'");
		}
		$memoryLimit = self::parseMemoryLimit(ini_get('memory_limit'));
		$chunkSize = !is_null($memoryLimit)
			? min(max((int) ($memoryLimit / 10), static::MIN_CHUNK_SIZE), static::MAX_CHUNK_SIZE)
			: static::DEFAULT_CHUNK_SIZE;
		stream_set_chunk_size($resource, $chunkSize);
		$this->resource = $resource;
	}

	/**
	 * @param string $message
	 * @return int
	 */
	public function write(string $message): int {
		if (is_null($this->resource)) {
			$this->initialize();
		}
		$result = fwrite($this->resource, $message);
		if ($result === false) {
			throw new \RuntimeException("Writing to stream failed");
		}
		return $result;
	}

}