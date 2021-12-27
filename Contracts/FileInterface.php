<?php
namespace Pandora3\Contracts;

/**
 * Interface FileInterface
 * @package Pandora3\Contracts
 */
interface FileInterface {

	/**
	 * @return string
	 */
	function getPath(): string;

	/**
	 * @return int|null
	 */
	function getSize(): ?int;

	/**
	 * @return string
	 */
	function getName(): string;

	/**
	 * @return string
	 */
	function getNameWithoutExtension(): string;

	/**
	 * @return string
	 */
	function getExtension(): string;


}