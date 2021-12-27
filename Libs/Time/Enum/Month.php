<?php
namespace Pandora3\Time\Enum;

/**
 * Class Month
 * @package Pandora3\Time\Enum
 */
class Month {

	public const None = 0;

	public const January = 1;
	public const February = 2;
	public const March = 3;
	public const April = 4;
	public const May = 5;
	public const June = 6;
	public const July = 7;
	public const August = 8;
	public const September = 9;
	public const October = 10;
	public const November = 11;
	public const December = 12;

	/**
	 * @param int $month
	 * @param int|null $year
	 * @return int
	 */
	public static function numberOfDays(int $month, ?int $year = null): int {
		if (is_null($year)) {
			$year = (int) date('Y');
		}
		return cal_days_in_month(CAL_GREGORIAN, $month, $year);
	}

}