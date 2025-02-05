<?php
namespace Pandora3\Contracts;

/**
 * Application level exception which has a message displayable for user
 * does not flash in dev or test modes
 * Interface ApplicationExceptionInterface
 * @package Pandora3\Contracts
 */
interface ApplicationExceptionInterface {
	
	/**
	 * @return string
	 */
	function getMessage();

}