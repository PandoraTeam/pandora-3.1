<?php
namespace Pandora3\Validator\Rules;
use Pandora3\Validator\BaseRule;

/**
 * Class RuleDate
 * @package Pandora3\Validator\Rules
 */
class RuleDate extends BaseRule {

	/** @var string */
	public $message = 'Field "{:field}" date format is not valid';
	
	/**
	 * RuleFile constructor
	 * @param array $arguments
	 */
	public function __construct(array $arguments = []) { }
	
	/**
	 * @param mixed|null $value
	 * @param array $values
	 * @return bool
	 */
	public function validate($value, array $values = []): bool {
		return (
			!$value ||
			$value instanceof \DateTimeInterface
		);
	}

}