<?php
namespace Pandora3\Time;

/**
 * Trait HasTranslation
 * @package Pandora3\Time
 */
trait HasTranslation {

	/** @var array */
	protected static $translations = [
		'ru' => [
			'month' => [
				'January' =>	'Январь',
				'February' =>	'Февраль',
				'March' =>		'Март',
				'April' =>		'Апрель',
				'May' =>		'Май',
				'June' =>		'Июнь',
				'July' =>		'Июль',
				'August' =>		'Август',
				'September' =>	'Сентябрь',
				'October' =>	'Октябрь',
				'November' =>	'Ноябрь',
				'December' =>	'Декабрь',
			],
			'monthCase' => [
				'January' =>	'Января',
				'February' =>	'Февраля',
				'March' =>		'Марта',
				'April' =>		'Апреля',
				'May' =>		'Мая',
				'June' =>		'Июня',
				'July' =>		'Июля',
				'August' =>		'Августа',
				'September' =>	'Сентября',
				'October' =>	'Октября',
				'November' =>	'Ноября',
				'December' =>	'Декабря',
			],
			'monthShort' => [
				'Jan' => 'Янв',
				'Feb' => 'Фев',
				'Mar' => 'Мар',
				'Apr' => 'Апр',
				'May' => 'Май',
				'Jun' => 'Июн',
				'Jul' => 'Июл',
				'Aug' => 'Авг',
				'Sep' => 'Сен',
				'Oct' => 'Окт',
				'Nov' => 'Ноя',
				'Dec' => 'Дек',
			],
			'weekDay' => [
				'Monday' =>		'Понедельник',
				'Tuesday' =>	'Вторник',
				'Wednesday' =>	'Среда',
				'Thursday' =>	'Четверг',
				'Friday' =>		'Пятница',
				'Saturday' =>	'Суббота',
				'Sunday' =>		'Воскресенье',
			],
			'weekDayShort' => [
				'Mon' => 'Пн',
				'Tue' => 'Вт',
				'Wed' => 'Ср',
				'Thu' => 'Чт',
				'Fri' => 'Пт',
				'Sat' => 'Сб',
				'Sun' => 'Вс',
			],
		]
	];

	/** @var array */
	protected static $translationKeys = [
		'F' => 'month',
		'P' => 'monthCase',
		'M' => 'monthShort',
		'l' => 'weekDay',
		'D' => 'weekDayShort',
	];
	
	/**
	 * @param string $format
	 * @param string $locale
	 * @return string
	 */
	public function translate(string $format, string $locale): string {
		$translation = self::$translations[$locale] ?? null;
		if (is_null($translation)) {
			return $format;
		}
		return preg_replace_callback('/[FPMlD]/', function($matches) use ($translation) {
			$segment = $matches[0];
			$formatted = parent::format(($segment === 'P') ? 'F' : $segment);
			$key = self::$translationKeys[$segment];
			return $translation[$key][$formatted] ?? $formatted;
		}, $format);
	}

}