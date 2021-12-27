<?php
namespace Pandora3\Sanitizer\Filters;

/**
 * Class FilterDate
 * @package Pandora3\Sanitizer\Filters
 */
class FilterDate {

	/** @var string */
	protected $format;

	/**
	 * FilterDate constructor
	 * @param array $arguments
	 */
	public function __construct(array $arguments) {
		if (!isset($arguments['param'])) {
			throw new \LogicException("Filter date parameter required");
		}
		$this->format = $arguments['param'];
	}

	/**
	 * @param mixed|null $value
	 * @return mixed|null
	 */
	public function apply($value) {
		if (!$value) {
			return $value;
		}
		$date = \DateTime::createFromFormat($this->format, $value);
		return $date ? $date->setTime(0, 0) : $value;
	}

}