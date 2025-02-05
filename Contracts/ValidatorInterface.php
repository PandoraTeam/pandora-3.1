<?php
namespace Pandora3\Contracts;

/**
 * Interface ValidatorInterface
 * @package Pandora3\Contracts
 */
interface ValidatorInterface {

	/**
	 * @return array
	 */
	function getMessages(): array;

	/**
	 * @param string $fieldName
	 * @return string
	 */
	function getFieldMessage(string $fieldName): string;
	
	/**
	 * @param array $labels
	 */
	function setFieldLabels(array $labels): void;

	/**
	 * Validate values
	 * @param RequestInterface|array $data
	 */
	function validate($data): void;

}