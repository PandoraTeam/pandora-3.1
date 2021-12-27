<?php
namespace Pandora3\Contracts;

/**
 * Interface UserProviderInterface
 * @package Pandora3\Contracts
 */
interface UserProviderInterface {
	
	/**
	 * @param int|string $id
	 * @return AuthenticationUserInterface|null
	 */
	function getUserById($id): ?AuthenticationUserInterface;

	/**
	 * @param string $login
	 * @return AuthenticationUserInterface|null
	 */
	function getUserByLogin(string $login): ?AuthenticationUserInterface;

}