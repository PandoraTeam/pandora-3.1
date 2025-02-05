<?php
namespace Pandora3\Contracts;

/**
 * Interface LogHandlerInterface
 * @package Pandora3\Contracts
 */
interface LogHandlerInterface {
	
	/**
	 * @param object $record
	 */
	function handle(object $record): void;
	
	/**
	 * @return string
	 */
	function getLevel(): string;

}