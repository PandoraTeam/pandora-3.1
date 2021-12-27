<?php
namespace Pandora3\Validator\Rules;

/**
 * Class RuleEmail
 * @package Pandora3\Validator\Rules
 */
class RuleEmail {

	private const PATTERN_EMAIL = '/^[\w\._-]+@[\w\._-]+\.\w(?:[\w\.]*\w)?$/';
	
	/** @var string */
	public $message = 'Field "{:field}" should be a valid email address'; // 'Неверный формат поля "{:field}"'
	
	/**
	 * RuleEmail constructor
	 * @param array $arguments
	 */
	public function __construct(array $arguments = []) { }

	/**
	 * @param mixed|null $value
	 * @param array $values
	 * @return bool
	 */
	public function validate($value, array $values = []): bool {
		if (empty($value)) {
			return true;
		}
		return preg_match(self::PATTERN_EMAIL, $value);
	}

}