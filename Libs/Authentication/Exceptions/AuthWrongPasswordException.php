<?php
namespace Pandora3\Authentication\Exceptions;

use Pandora3\Contracts\ApplicationLogicExceptionInterface;

/**
 * Class AuthWrongPasswordException
 * @package Pandora3\Authentication\Exceptions
 */
class AuthWrongPasswordException extends \LogicException implements ApplicationLogicExceptionInterface {
	
	/**
	 * AuthWrongPasswordException constructor
	 */
	public function __construct() {
		parent::__construct("Wrong password", E_WARNING);
	}
	
}