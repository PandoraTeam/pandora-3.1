<?php
namespace Pandora3\Form\Exceptions;

use Pandora3\Contracts\ValidationExceptionInterface;

/**
 * Class ValidationException
 * @package Pandora3\Validator\Exceptions
 */
class ValidationFormException extends \LogicException implements ValidationExceptionInterface {

	/** @var array */
	protected $messages;
	
	/** @var string|null */
	protected $formName;

	/**
	 * NoMatchedRouteException constructor
	 * @param array|string $messages
	 * @param string|null $formName
	 */
	public function __construct($messages, ?string $formName = null) {
		parent::__construct("Validation failed", E_WARNING);
		$this->messages = is_array($messages)
			? $messages : [$messages];
		$this->formName = $formName;
	}

	/**
	 * @return array
	 */
	public function getMessages(): array {
		return $this->messages;
	}
	
	/**
	 * @return null|string
	 */
	function getFormName(): ?string {
		return $this->formName;
	}
	
}