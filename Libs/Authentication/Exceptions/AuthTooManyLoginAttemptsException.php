<?php
namespace Pandora3\Authentication\Exceptions;

use Pandora3\Contracts\ApplicationLogicExceptionInterface;

/**
 * Class AuthTooManyLoginAttemptsException
 * @package Pandora3\Authentication\Exceptions
 */
class AuthTooManyLoginAttemptsException extends \LogicException implements ApplicationLogicExceptionInterface {
	
	/**
	 * AuthTooManyLoginAttemptsException constructor
	 */
	public function __construct() {
		parent::__construct("Too many login attempts", E_WARNING);
	}
	
}