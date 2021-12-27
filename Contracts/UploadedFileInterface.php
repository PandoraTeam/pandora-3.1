<?php
namespace Pandora3\Contracts;

/**
 * Interface UploadedFileInterface
 * @package Pandora3\Contracts
 */
interface UploadedFileInterface extends FileInterface {

	/**
	 * @param string $directory
	 * @param null|string $name
	 * @return FileInterface
	 */
	function move(string $directory, ?string $name = null): FileInterface;

	/**
	 * @return string
	 */
	function getUploadName(): string;

	/**
	 * @return null|string
	 */
	function getMediaType(): ?string;

	/**
	 * @return int|null
	 */
	function getError(): ?int;

}