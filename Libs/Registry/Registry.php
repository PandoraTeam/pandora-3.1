<?php
namespace Pandora3\Registry;

/**
 * Class Registry
 * @package Pandora3\Registry
 */
class Registry {
	
	/** @var array */
	protected $items;
	
	/**
	 * @param array $items
	 */
	public function __construct(array $items) {
		$this->items = $items;
	}
	
	/**
	 * @param string $key
	 * @param mixed|null $value
	 */
	public function set(string $key, $value): void {
		/* if (strpos($key, '.') === false) {
			$this->items[$key] = $value;
			return;
		} */
		
		$segments = explode('.', $key);
		$lastIndex = count($segments) - 1;
		if ($lastIndex === 0) {
			$this->items[$key] = $value;
			return;
		}
		$items = &$this->items;
		foreach ($segments as $i => $segment) {
			if ($i === $lastIndex) {
				$items[$segment] = $value;
				break;
			}
			if (!is_array($items[$segment] ?? null)) {
				$items[$segment] = [];
			}
			$items = &$items[$segment];
		}
		
		// $items[$segments[$lastIndex]] = $value;
	}
	
	/**
	 * @param string $key
	 */
	public function remove(string $key): void {
		/* if (strpos($key, '.') === false) {
			unset($this->items[$key]);
		} */
		
		$segments = explode('.', $key);
		$lastIndex = count($segments) - 1;
		if ($lastIndex === 0) {
			unset($this->items[$key]);
			return;
		}
		$items = &$this->items;
		foreach ($segments as $i => $segment) {
			if ($i === $lastIndex) {
				unset($items[$segment]);
				break;
			}
			if (!is_array($items[$segment] ?? null)) {
				break;
			}
			$items = &$items[$segment];
		}
		
		// unset($items[$segments[$removeSegmentIndex]]);
	}
	
	/**
	 * @param string $key
	 * @return bool
	 */
	public function has(string $key): bool {
		if (strpos($key, '.') === false) {
			return isset($this->items[$key]);
		}
		
		$segments = explode('.', $key);
		$value = $this->items;
		foreach ($segments as $segment) {
			if (!is_scalar($value) && isset($value[$segment])) {
				$value = $value[$segment];
			} else {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * @param string $key
	 * @param null $default
	 * @return mixed|null
	 */
	public function get(string $key, $default = null) {
		if (strpos($key, '.') === false) {
			return $this->items[$key] ?? $default;
		}

		$segments = explode('.', $key);
		$value = $this->items;
		foreach ($segments as $segment) {
			if (!is_scalar($value) && isset($value[$segment])) {
				$value = $value[$segment];
			} else {
				return $default;
			}
		}
		
		return $value;
	}
	
	/**
	 * @return array
	 */
	public function items(): array {
		return $this->items;
	}
	
}