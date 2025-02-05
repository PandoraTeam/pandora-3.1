<?php
namespace Pandora3\Validator\Rules;
use Pandora3\Validator\BaseRule;

/**
 * Class RuleEqual
 * @package Pandora3\Validator\Rules
 */
class RuleEqual extends BaseRule {

	// public $message = ''; // 'Field "{:field}" must be equal to {:compareField}'; // todo: {:compareField}
	
	/** @var string */
	protected $compareField;
	
	/**
	 * RuleEqual constructor
	 * @param array $arguments
	 */
	public function __construct(array $arguments = []) {
		if (!isset($arguments['param'])) {
			throw new \LogicException("Rule equal parameter required");
		}
		$this->compareField = $arguments['param'];
	}

	/**
	 * @param mixed|null $value
	 * @param array $values
	 * @return bool
	 */
	public function validate($value, array $values = []): bool {
		$compareValue = $values[$this->compareField] ?? null;
		$this->setMessage('Field "{:field}" must be equal to {:compareField}', ['compareField' => $this->compareField]);
		return (
			!is_null($compareValue) &&
			$value === $compareValue
		);
	}

}