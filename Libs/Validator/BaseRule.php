<?php
namespace Pandora3\Validator;

abstract class BaseRule {

	/** @var string */
	public $message = '';
	
	/** @var array */
	public $messageParams = [];
	
	/**
	 * @param string $message
	 * @param array $params
	 */
	protected function setMessage(string $message, array $params = []): void {
		$this->message = $message;
		$this->messageParams = $params;
	}
	
	/**
	 * @param mixed|null $value
	 * @param array $values
	 * @return bool
	 */
	abstract public function validate($value, array $values = []): bool;

}