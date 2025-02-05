<?php
namespace Pandora3\Sanitizer\Filters;

/**
 * Class FilterCast
 * @package Pandora3\Sanitizer\Filters
 */
class FilterCast {

	/** @var string */
	protected $type;

	/**
	 * FilterCast constructor
	 * @param array $arguments
	 */
	public function __construct(array $arguments) {
		if (!isset($arguments['param'])) {
			throw new \LogicException("Filter cast parameter required");
		}
		$this->type = $arguments['param'];
	}

	/**
	 * @param mixed|null $value
	 * @return mixed|null
	 */
	public function apply($value) {
		switch ($this->type) {
			case 'int':
				return (int) $value;
			case 'float':
				return (float) $value;
			case 'string':
				return (string) $value;
			case 'bool':
				return (bool) $value;
			case 'object':
				return is_string($value)
					? json_decode($value, false)
					: (object) $value;
			case 'array':
				return is_string($value)
					? json_decode($value, true)
					: (array) $value;
			/* case 'collection':
				$array = is_array($value) ? $value : json_decode($value, true);
				return new Collection($array); */
			default:
				throw new \LogicException("Filter cast unsupported type: '{$this->type}'");
		}
	}

}