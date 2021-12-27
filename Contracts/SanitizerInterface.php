<?php
namespace Pandora3\Contracts;

/**
 * Interface SanitizerInterface
 * @package Pandora3\Contracts
 */
interface SanitizerInterface {

	/**
	 * Sanitize values
	 * @param RequestInterface|array $data
	 * @return array
	 */
	function sanitize($data): array;

}