<?php
namespace Pandora3\Contracts;

/**
 * Interface ResponseInterface
 * @package Pandora3\Contracts
 */
interface ResponseInterface {

	/**
	 * Send response
	 */
	function send(): void;
	
	/**
	 * @return int
	 */
	function getStatus(): int;

}