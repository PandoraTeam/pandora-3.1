<?php
namespace Pandora3\Sanitizer\Filters;

/**
 * Class FilterCapitalize
 * @package Pandora3\Sanitizer\Filters
 */
class FilterCapitalize {

	/**
	 * FilterCapitalize constructor
	 * @param array $arguments
	 */
	public function __construct(array $arguments = []) { }

	/**
	 * @param mixed|null $value
	 * @return mixed|null
	 */
	public function apply($value) {
		return is_string($value)
			? mb_convert_case(mb_strtolower($value, 'UTF-8'),  MB_CASE_TITLE)
			: $value;
	}

}