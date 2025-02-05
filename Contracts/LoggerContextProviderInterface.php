<?php
namespace Pandora3\Contracts;

/**
 * Interface LoggerContextProvider
 * @package Pandora3\Contracts
 */
interface LoggerContextProviderInterface {
	
	/**
	 * @return array
	 */
	function getContext(): array;

}