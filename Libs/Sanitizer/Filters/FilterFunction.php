<?php
namespace Pandora3\Sanitizer\Filters;

/**
 * Class FilterFunction
 * @package Pandora3\Sanitizer\Filters
 */
class FilterFunction {

	/** @var \Closure */
	protected $callback;
	
	/**
	 * FilterFunction constructor
	 * @param \Closure $callback
	 */
	public function __construct(\Closure $callback) {
		$this->callback = $callback;
	}
	
	/**
	 * @param mixed|null $value
	 * @return mixed|null
	 */
	public function apply($value) {
		$callback = $this->callback;
		return $callback($value);
	}

}