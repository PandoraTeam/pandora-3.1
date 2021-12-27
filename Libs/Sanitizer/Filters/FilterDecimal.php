<?php
namespace Pandora3\Sanitizer\Filters;

/**
 * Class FilterDecimal
 * @package Pandora3\Sanitizer\Filters
 */
class FilterDecimal {

	/**
	 * FilterDecimal constructor
	 * @param array $arguments
	 */
	public function __construct(array $arguments = []) { }

	/**
	 * @param mixed|null $value
	 * @return mixed|null
	 */
	public function apply($value) {
		if (is_int($value) || is_float($value)) {
			return (string) $value;
		}
		if (is_string($value)) {
			return (string) ((float) $value); // todo: maybe use regex
		}
		return $value;
	}

}