<?php
namespace Pandora3\Sanitizer\Filters;

/**
 * Class FilterUpper
 * @package Pandora3\Sanitizer\Filters
 */
class FilterUpper {

	/**
	 * FilterUpper constructor
	 * @param array $arguments
	 */
	public function __construct(array $arguments = []) { }

	/**
	 * @param mixed|null $value
	 * @return mixed|null
	 */
	public function apply($value) {
		return is_string($value)
			? mb_strtoupper($value, 'UTF-8')
			: $value;
	}

}