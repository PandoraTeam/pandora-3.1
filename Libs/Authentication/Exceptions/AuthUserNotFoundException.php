<?php
namespace Pandora3\Authentication\Exceptions;

use Pandora3\Contracts\ApplicationLogicExceptionInterface;

/**
 * Class AuthUserNotFoundException
 * @package Pandora3\Authentication\Exceptions
 */
class AuthUserNotFoundException extends \LogicException implements ApplicationLogicExceptionInterface {

	/** @var string */
	protected $login;
	
	/**
	 * AuthUserNotFoundException constructor.
	 * @param string $login
	 */
	public function __construct(string $login) {
		parent::__construct("User not found", E_WARNING);
		$this->login = $login;
	}
	
	/**
	 * @return string
	 */
	public function getLogin(): string {
		return $this->login;
	}
	
}