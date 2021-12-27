<?php
namespace Pandora3\Eloquent;

use Pandora3\Time\Date;
use Pandora3\Time\DateTime;

/**
 * Trait HasEloquentDateTime
 * @package Pandora3\Eloquent
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasEloquentDateTime {
	
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
			return new DateTime($value);
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