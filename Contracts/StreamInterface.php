<?php
namespace Pandora3\Contracts;

/**
 * Interface StreamInterface
 * @package Pandora3\Contracts
 */
interface StreamInterface {

	/**
	 * @param string $message
	 * @return int
	 */
	function write(string $message): int;

}