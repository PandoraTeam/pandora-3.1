<?php
namespace Pandora3\Contracts;

/**
 * Interface StorageInterface
 * @package Pandora3\Contracts
 */
interface StorageInterface {
	
	/**
	 * @return string
	 */
	function getPath(): string;

	/**
	 * @param UploadedFileInterface|string $file
	 * @param string $path
	 * @param bool $move
	 * @return bool
	 */
	function upload($file, string $path, bool $move = true): bool;

	/**
	 * @param string $path
	 * @return bool
	 */
	function delete(string $path): bool;

}