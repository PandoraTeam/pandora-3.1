<?php
namespace Pandora3\Sanitizer\Filters;

/**
 * Class FilterLower
 * @package Pandora3\Sanitizer\Filters
 */
class FilterLower {

	/**
	 * FilterLower constructor
	 * @param array $arguments
	 */
	public function __construct(array $arguments = []) { }

	/**
	 * @param mixed|null $value
	 * @return mixed|null
	 */
	public function apply($value) {
		return is_string($value)
			? mb_strtolower($value, 'UTF-8')
			: $value;
	}

}