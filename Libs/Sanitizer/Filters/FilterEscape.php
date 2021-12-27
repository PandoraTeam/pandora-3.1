<?php
namespace Pandora3\Sanitizer\Filters;

/**
 * Class FilterEscape
 * @package Pandora3\Sanitizer\Filters
 */
class FilterEscape {

	/**
	 * FilterEscape constructor
	 * @param array $arguments
	 */
	public function __construct(array $arguments = []) { }

	/**
	 * @param mixed|null $value
	 * @return mixed|null
	 */
	public function apply($value) {
		return is_string($value)
			? filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)
			: $value;
	}

}