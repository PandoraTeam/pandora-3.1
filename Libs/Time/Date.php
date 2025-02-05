<?php
namespace Pandora3\Time;

use DateInterval;
use DateTimeZone;
use DateTimeInterface;

/**
 * Class Date
 * Immutable date representation
 * @package Pandora3\Time
 *
 * @property-read int $year
 * @property-read int $month
 * @property-read int $day
 * @property-read int $dayOfWeek
 */
class Date extends \DateTimeImmutable {

	use HasTranslation;

	public const FormatMysql = 'Y-m-d';

	protected static $globalLocale = 'en';

	protected $locale;
	
	/**
	 * Date constructor
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
		parent::__construct($time->format(self::FormatMysql), $timezone);
		$this->locale = self::$globalLocale;
	}

	/**
	 * @param string|int $year
	 * @param string|int $month
	 * @param string|int $day
	 * @return static
	 */
	public static function create($year, $month, $day): self {
		return (new static())
			->setDate((int) $year, (int) $month, (int) $day);
	}

	/**
	 * @param string $format
	 * @param string|null $time
	 * @param string|DateTimeZone|null $timezone
	 * @return static|null
	 */
	public static function createFromFormat($format, $time, $timezone = null) {
		$date = $time ? parent::createFromFormat($format, $time) : null; // todo: think
		return $date ? new static($date, $timezone) : null;
	}

	/**
	 * @param string $locale
	 */
	public static function setLocale(string $locale): void {
		self::$globalLocale = $locale;
	}

	/**
	 * @return string
	 */
	public static function getLocale(): string {
		return self::$globalLocale;
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
	 * @param int $year
	 * @return bool
	 */
	public static function isLeapYear(int $year): bool {
		$date = \DateTime::createFromFormat('Y', $year);
		return (int) $date->format('L') === 1;
	}

	/**
	 * @return DateTime
	 */
	public function toDateTime(): DateTime {
		return new DateTime($this);
	}
	
	/**
	 * @param DateTimeInterface $date
	 * @return bool
	 */
	public function isEqualTo(DateTimeInterface $date): bool {
		return $this->getTimestamp() === $date->getTimestamp();
	}

	/**
	 * @param string $interval
	 * @return static
	 */
	public function addInterval(string $interval) {
		$date = $this->add(DateInterval::createFromDateString($interval));
		return $date->setTime(0, 0);
	}

	/**
	 * @param string $interval
	 * @return static
	 */
	public function subInterval(string $interval) {
		$date = $this->sub(DateInterval::createFromDateString($interval));
		return $date->setTime(0, 0);
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