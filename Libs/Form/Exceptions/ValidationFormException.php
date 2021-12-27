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

	/**
	 * NoMatchedRouteException constructor
	 * @param string $message
	 */
	public function __construct(string $message) {
		parent::__construct("Validation failed", E_WARNING);
		$this->messages = [$message];
	}

	/**
	 * @return array
	 */
	public function getMessages(): array {
		return $this->messages;
	}

}