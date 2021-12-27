<?php
namespace Pandora3\Validator\Rules;

/**
 * Class RuleUnique
 * @package Pandora3\Validator\Rules
 */
class RuleUnique {

	/** @var string */
	public $message = 'Field "{:field}" must be unique';
	
	/** @var \Closure */
	protected $isUnique;
	
	/**
	 * RuleUnique constructor
	 * @param array $arguments
	 */
	public function __construct(array $arguments) {
		if (!isset($arguments['param'])) {
			throw new \LogicException("Rule unique parameter required");
		}
		$this->isUnique = $arguments['param'];
	}
	
	/**
	 * @param mixed|null $value
	 * @param array $values
	 * @return bool
	 */
	public function validate($value, array $values = []): bool {
		if (!$value) {
			return true;
		}
		$isUnique = $this->isUnique;
		return $isUnique($value);
	}

}