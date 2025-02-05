<?php
namespace Pandora3\Contracts;

/**
 * Interface DispatcherInterface
 * @package Pandora3\Contracts
 */
interface DispatcherInterface {

	/**
	 * @param string $eventClass
	 * @param \Closure $listener
	 */
	function listen(string $eventClass, \Closure $listener);
	
	/**
	 * @param object $event
	 * @return object
	 */
	function dispatch(object $event): object;

}