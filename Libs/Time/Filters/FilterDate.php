<?php
namespace Pandora3\Time\Filters;

use Pandora3\Time\Date;

/**
 * Class FilterDate
 * @package Pandora3\Time\Filters
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
			return null;
		}
		if ($value instanceof \DateTimeInterface) {
			return new Date($value);
		}
		return Date::createFromFormat($this->format, $value) ?? $value;
	}

}