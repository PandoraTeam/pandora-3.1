<?php
namespace Pandora3\Eloquent;

use Pandora3\Time\Date;
use Pandora3\Time\DateTime;
use Pandora3\Time\Time;

/**
 * Trait HasEloquentDateTime
 * @package Pandora3\Eloquent
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasEloquentDateTime {
	
	/**
	 * {@inheritdoc}
	 */
	protected function getCastType($key) {
		if ($this->isTimeCast($this->getCasts()[$key])) {
			return 'time';
		}
		
		return parent::getCastType($key);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function castAttribute($key, $value) {
		if (is_null($value)) {
			return $value;
		}
		
		if ($this->getCastType($key) === 'time') {
			return $this->asTime($value);
		}
		
		return parent::castAttribute($key, $value);
	}

	/**
	 * @param $cast
	 * @return bool
	 */
	protected function isTimeCast($cast) {
		return strncmp($cast, 'time:', 5) === 0;
	}
	
	/**
	 * @param mixed $value
	 * @return Time|null
	 */
	protected function asTime($value): ?Time {
		if (is_null($value)) {
			return null;
		}
		if ($value instanceof \DateTimeInterface) {
			return Time::createFromDateTime($value);
		}
		return Time::createFromFormat($value, Time::FormatMysql);
	}
	
	/**
	 * @param mixed $value
	 * @return Date|null
	 */
	protected function asDate($value): ?Date {
		if (is_null($value)) {
			return null;
		}
		if ($value instanceof \DateTimeInterface) {
			return new Date($value, $value->getTimezone());
		}
		if ($this->isStandardDateFormat($value)) {
			return Date::createFromFormat(Date::FormatMysql, $value);
		}
		$format = $this->getDateFormat();
		return Date::createFromFormat($format, $value);
	}
	
	/**
	 * @param mixed $value
	 * @return DateTime|null
	 */
	protected function asDateTime($value): ?DateTime {
		if (is_null($value)) {
			return null;
		}
		if ($value instanceof \DateTimeInterface) {
			return new DateTime($value, $value->getTimezone());
		}
		/* if (is_object($value)) {
			return parent::asDateTime($value);
		} */
		if (is_numeric($value)) {
			return DateTime::createFromTimestamp($value);
		}
		if ($this->isStandardDateFormat($value)) {
			return DateTime::createFromFormat(Date::FormatMysql, $value);
		}

		$format = $this->getDateFormat();
		/* // https://bugs.php.net/bug.php?id=75577
		if (version_compare(PHP_VERSION, '7.3.0-dev', '<')) {
			$format = str_replace('.v', '.u', $format);
		} */

		return DateTime::createFromFormat($format, $value);
		
		/* $value = parent::asDateTime($value);
		return $value ? new DateTime($value, $value->getTimezone()) : $value; */
	}

}