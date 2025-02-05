<?php
namespace Pandora3\Time;

use DateTimeInterface;

/**
 * Class Time
 * Immutable time representation
 * @package Pandora3\Time
 *
 * @property-read int $hour
 * @property-read int $minute
 * @property-read int $second
 * @property-read int $microseconds
 */
class Time {

	public const FormatMysql = 'H:i:s';
	
	/** @var int */
	protected $hour;
	
	/** @var int */
	protected $minute;
	
	/** @var int */
	protected $second;
	
	/** @var int */
	protected $microseconds;
	
	/**
	 * Time constructor
	 * @param int $hour
	 * @param int $minute
	 * @param int $second
	 * @param int $microseconds
	 */
	public function __construct(int $hour, int $minute, int $second = 0, int $microseconds = 0) {
        $this->second = $second + intdiv($microseconds, 1000000);
        $this->minute = $minute + intdiv($this->second, 60);
        $this->hour = $hour + intdiv($this->minute, 60);

        $this->microseconds = $microseconds % 1000000;
        $this->second %= 60;
        $this->minute %= 60;
        $this->hour %= 24;
	}
	
	/**
	 * @param DateTimeInterface $time
	 * @return Time
	 */
	public static function createFromDateTime(DateTimeInterface $time): self {
		$hour = (int) $time->format('H');
		$minute = (int) $time->format('i');
		$second = (int) $time->format('s');
		$microseconds = (int) $time->format('u');
		return new static($hour, $minute, $second, $microseconds);
	}
	
	/**
	 * @param string $time
	 * @param string $format
	 * @return Time|null
	 */
	public static function createFromFormat(string $time, string $format): ?self {
		$time = \DateTime::createFromFormat($format, $time);
		return static::createFromDateTime($time);
	}
	
	/**
	 * @ignore
	 * @param string $property
	 * @return mixed
	 */
	public function __get(string $property) {
		$methodName = 'get'.ucfirst($property);
		if (method_exists($this, $methodName)) {
			return $this->{$methodName}();
		}
		// $className = static::class;
		// logException(new \LogicException("Undefined property '$property' for [$className]", E_NOTICE));
		return null;
	}
	
	/**
	 * @internal
	 * @return int
	 */
	protected function getHour(): int {
		return $this->hour;
	}
	
	/**
	 * @internal
	 * @return int
	 */
	protected function getMinute(): int {
		return $this->minute;
	}
	
	/**
	 * @internal
	 * @return int
	 */
	protected function getSecond(): int {
		return $this->second;
	}
	
	/**
	 * @internal
	 * @return int
	 */
	protected function getMicroseconds(): int {
		return $this->microseconds;
	}
	
	// public function add(TimeInterval $interval): self { }
	
	public function modify(string $modify): self {
		$date = (new \DateTime())
			->setDate(2000, 1, 1)
			->setTime($this->hour, $this->minute, $this->second, $this->microseconds);
		$date->modify($modify);
		return static::createFromDateTime($date);
	}
	
	/**
	 * @param string $format
	 * @return string
	 */
	public function format(string $format): string {
		return preg_replace_callback('#\\\\?[gGhHisu]#', function($matches) {
			$segment = $matches[0];
			switch ($segment) {
				case 'g':
					return (string) ($this->hour % 12);
				case 'G':
					return (string) $this->hour;
				case 'h':
					return str_pad(($this->hour % 12), 2, '0', STR_PAD_LEFT);
				case 'H':
					return str_pad($this->hour, 2, '0', STR_PAD_LEFT);
				case 'i':
					return str_pad($this->minute, 2, '0', STR_PAD_LEFT);
				case 's':
					return str_pad($this->second, 2, '0', STR_PAD_LEFT);
				case 'u':
					return str_pad($this->microseconds, 6, '0', STR_PAD_LEFT);
			}
			return $segment;
		}, $format);
	}
	
}