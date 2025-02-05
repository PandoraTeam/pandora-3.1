<?php
namespace Pandora3\Validator\Rules;
use Pandora3\Validator\BaseRule;

/**
 * Class RuleFunction
 * @package Pandora3\Validator\Rules
 */
class RuleFunction extends BaseRule {

	/** @var \Closure */
	protected $callback;
	
	/**
	 * RuleFunction constructor
	 * @param \Closure $callback
	 */
	public function __construct(\Closure $callback) {
		$this->callback = $callback;
	}
	
	/**
	 * @param mixed|null $value
	 * @param array $values
	 * @return bool
	 */
	public function validate($value, array $values = []): bool {
		$callback = $this->callback;
		return $callback($value, function($message) {
			$this->message = $message;
		}, $values);
	}

}