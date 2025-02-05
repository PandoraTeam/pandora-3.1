<?php
namespace Pandora3\Time;

use DateInterval;
use DateTimeZone;
use DateTimeInterface;

/**
 * Class DateTime
 * Immutable date and time representation
 * @package Pandora3\Time
 *
 * @property-read int $year
 * @property-read int $month
 * @property-read int $day
 * @property-read int $dayOfWeek
 * @property-read int $hour
 * @property-read int $minute
 * @property-read int $second
 * @property-read int $microseconds
 * @property-read Date $date
 * @property-read Time $time
 */
class DateTime extends \DateTimeImmutable {

	use HasTranslation;

	public const FormatMysql = 'Y-m-d H:i:s';

	protected $locale;
	
	/**
	 * DateTime constructor
	 * @param string|DateTimeInterface $time
	 * @param string|DateTimeZone|null $timezone
	 */
	public function __construct($time = 'now', $timezone = null) {
	    if (is_string($timezone)) {
			$timezone = new DateTimeZone($timezone);
        }

		if (!is_object($time) || !($time instanceof DateTimeInterface)) {
			$time = new \DateTime($time);
		}
		parent::__construct($time->format('Y-m-d H:i:s.u'), $timezone);
		$this->locale = Date::getLocale();
	}

	/**
	 * @param int $year
	 * @param int $month
	 * @param int $day
	 * @param int $hour
	 * @param int $minute
	 * @param int $second
	 * @return static
	 */
	public static function create(int $year, int $month, int $day, int $hour = 0, int $minute = 0, int $second = 0): self {
		return (new static())
			->setDate($year, $month, $day)
			->setTime($hour, $minute, $second);
	}
	
	/**
	 * @param int $timestamp
	 * @param string|DateTimeZone|null $timezone
	 * @return static
	 */
	public static function createFromTimestamp(int $timestamp, DateTimeZone $timezone = null): self {
		$date = new static($timestamp);
		return new static($date, $timezone); // todo: не тестировалось
	}

	/**
	 * @param string $format
	 * @param string|null $time
	 * @param string|DateTimeZone|null $timezone
	 * @return static|null
	 */
	public static function createFromFormat($format, $time, DateTimeZone $timezone = null) {
		$date = $time ? parent::createFromFormat($format, $time) : null; // todo: think
		return $date ? new static($date, $timezone) : null;
	}
	
	/**
	 * @param string $format
	 * @return string
	 */
	public function format($format) {
		$format = $this->translate($format, $this->locale);
		return parent::format($format);
	}

	/**
	 * @param string $locale
	 * @return static
	 */
	public function localized(string $locale): self {
		$date = clone $this;
		$date->locale = $locale;
		return $date;
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
	 * @return static
	 */
	public static function now(): self {
		return new static('now');
	}

	/**
	 * @return string
	 */
	public function toMysql(): string {
		return $this->format(self::FormatMysql);
	}

	/**
	 * @internal
	 * @return int
	 */
	protected function getYear(): int {
		return (int) $this->format('Y');
	}

	/**
	 * @internal
	 * @return int
	 */
	protected function getMonth(): int {
		return (int) $this->format('m');
	}

	/**
	 * @internal
	 * @return int
	 */
	protected function getDay(): int {
		return (int) $this->format('d');
	}

	/**
	 * @internal
	 * Gets day of week 1 (for Monday) through 7 (for Saturday)
	 * @return int
	 */
	protected function getDayOfWeek(): int {
		return (int) $this->format('N');
	}

	/**
	 * @internal
	 * @return int
	 */
	protected function getHour(): int {
		return (int) $this->format('H');
	}

	/**
	 * @internal
	 * @return int
	 */
	protected function getMinute(): int {
		return (int) $this->format('i');
	}

	/**
	 * @internal
	 * @return int
	 */
	protected function getSecond(): int {
		return (int) $this->format('s');
	}
	
	/**
	 * @internal
	 * @return int
	 */
	protected function getMicroseconds(): int {
		return (int) $this->format('u');
	}

	/**
	 * @param string $interval
	 * @return static
	 */
	public function addInterval(string $interval) {
		return $this->add(DateInterval::createFromDateString($interval));
	}

	/**
	 * @param string $interval
	 * @return bool|static
	 */
	public function subInterval(string $interval) {
		return $this->sub(DateInterval::createFromDateString($interval));
	}

	/**
	 * @return Date
	 */
	public function getDate(): Date {
		return new Date($this);
	}
	
	/**
	 * @return Time
	 */
	public function getTime(): Time {
		return Time::createFromDateTime($this);
	}
	
	/**
	 * @param Time $time
	 * @return static
	 */
	public function setTimeFrom(Time $time): self {
		return $this->setTime($time->hour, $time->minute, $time->second, $time->microseconds);
	}

	/**
	 * @param string|DateTimeInterface|null $date
	 * @param string $format
	 * @return string
	 */
	public static function convert($date, string $format): string {
		if (!($date instanceof DateTimeInterface)) {
			$date = self::createFromFormat(self::FormatMysql, $date);
		}
		return $date ? $date->format($format) : '';
	}

}