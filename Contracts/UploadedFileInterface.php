<?php
namespace Pandora3\Contracts;

/**
 * Interface UploadedFileInterface
 * @package Pandora3\Contracts
 */
interface UploadedFileInterface extends FileInterface {

	/**
	 * @param string $directory
	 * @param string|null $name
	 * @return FileInterface
	 */
	function move(string $directory, ?string $name = null): FileInterface;

	/**
	 * @return string
	 */
	function getUploadName(): string;
	
	/**
	 * @return string
	 */
	function getUploadNameWithoutExtension(): string;
	
	/**
	 * @return string
	 */
	function getExtension(): string;

	/**
	 * @return string|null
	 */
	function getMediaType(): ?string;
	
	/**
	 * @return int|null
	 */
	function getSize(): ?int;

	/**
	 * @return int|null
	 */
	function getError(): ?int;

}