<?php
namespace Pandora3\File;

/**
 * Class FileUtils
 * @package Pandora3\File
 */
class FileUtils {

	/**
	 * @param string $path
	 */
	public static function createPath(string $path): void {
		if (is_dir($path)) {
			return;
		}
		if (!mkdir($path, 0755, true)) {
			throw new \RuntimeException("Failed to create path '$path'");
		}
	}

	/**
	 * @param string $filename
	 * @return string
	 */
	public static function getFileExtension(string $filename): string {
		return (strpos($filename, '.') !== false)
			? strtolower(pathinfo($filename, PATHINFO_EXTENSION))
			: '';
	}

	/**
	 * @param string $filename
	 * @return string
	 */
	public static function getFileName(string $filename): string {
		return pathinfo($filename, PATHINFO_FILENAME);
	}
	
	/**
	 * @param string $filename
	 * @return int|null
	 */
	public static function getFileSize(string $filename): ?int {
		$fileSize = filesize($filename);
		if ($fileSize === false) {
			return null;
		}
		return $fileSize;
	}

}