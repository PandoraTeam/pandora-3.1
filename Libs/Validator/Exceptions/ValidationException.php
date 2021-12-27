<?php
namespace Pandora3\Validator\Exceptions;

use Pandora3\Contracts\ValidationExceptionInterface;

/**
 * Class ValidationException
 * @package Pandora3\Validator\Exceptions
 */
class ValidationException extends \LogicException implements ValidationExceptionInterface {

	/** @var array */
	protected $messages;

	/**
	 * NoMatchedRouteException constructor
	 * @param array $messages
	 */
	public function __construct(array $messages) {
		parent::__construct("Validation failed", E_WARNING);
		$this->messages = $messages;
	}

	/**
	 * @return array
	 */
	public function getMessages(): array {
		return $this->messages;
	}

}