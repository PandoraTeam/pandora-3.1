<?php
namespace Pandora3\Contracts;

/**
 * Interface AuthenticationUserInterface
 * @package Pandora3\Contracts
 */
interface AuthenticationUserInterface {

	/**
	 * @return int|string
	 */
	function getAuthenticationId();

	/**
	 * @param string $password
	 * @return bool
	 */
	function checkPassword(string $password): bool;

}