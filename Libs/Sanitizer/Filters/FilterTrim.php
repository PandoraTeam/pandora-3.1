<?php
namespace Pandora3\Sanitizer\Filters;

/**
 * Class FilterTrim
 * @package Pandora3\Sanitizer\Filters
 */
class FilterTrim {

	/**
	 * FilterTrim constructor
	 * @param array $arguments
	 */
	public function __construct(array $arguments = []) { }

	/**
	 * @param mixed|null $value
	 * @return mixed|null
	 */
	public function apply($value) {
		return is_string($value)
			? trim($value)
			: $value;
	}

}