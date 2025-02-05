<?php
namespace Pandora3\Validator\Rules;
use Pandora3\Validator\BaseRule;

/**
 * Class RuleDecimal
 * @package Pandora3\Validator\Rules
 */
class RuleDecimal extends BaseRule {

	private const PATTERN_DECIMAL = '/^-?\d+(\.\d+)?$/';
	
	/** @var string */
	public $message = 'Field "{:field}" should be a decimal number'; // 'Неверный формат поля "{:field}"'
	
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
		return preg_match(self::PATTERN_DECIMAL, $value);
	}

}