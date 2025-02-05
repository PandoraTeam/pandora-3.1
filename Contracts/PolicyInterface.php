<?php
namespace Pandora3\Contracts;

/**
 * Interface PolicyInterface
 * @package Pandora3\Contracts
 */
interface PolicyInterface {

	/**
	 * @param AuthenticationUserInterface $user
	 * @param string $action
	 * @return bool|null
	 */
	function before(AuthenticationUserInterface $user, string $action): ?bool;

}