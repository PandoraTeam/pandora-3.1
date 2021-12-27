<?php
namespace Pandora3\Contracts;

/**
 * Application level exception which has a message displayable for user
 * Interface ApplicationExceptionInterface
 * @package Pandora3\Contracts
 */
interface ApplicationExceptionInterface {
	
	/**
	 * @return string
	 */
	public function getMessage();

}