<?php
namespace Pandora3\Contracts;

/**
 * Interface LoggerInterface
 * @package Pandora3\Contracts
 */
interface LoggerInterface {
	
	/**
	 * System is unusable
	 *
	 * @param string $message
	 * @param array $context
	 */
	function emergency(string $message, array $context = []): void;
	
	/**
	 * Action must be taken immediately
	 *
	 * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up
	 *
	 * @param string $message
	 * @param array $context
	 */
	function alert(string $message, array $context = []): void;
	
	/**
	 * Critical conditions. Application component unavailable, unexpected exception
	 *
	 * @param string $message
	 * @param array $context
	 */
	function critical(string $message, array $context = []): void;
	
	/**
	 * Runtime errors that do not require immediate action but should typically
     * be logged and monitored
	 *
	 * @param string $message
	 * @param array $context
	 */
	function error(string $message, array $context = []): void;
	
	/**
	 * Exceptional occurrences that are not errors
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong
	 *
	 * @param string $message
	 * @param array $context
	 */
	function warning(string $message, array $context = []): void;
	
	/**
	 * Normal but significant events
	 *
	 * @param string $message
	 * @param array $context
	 */
	function notice(string $message, array $context = []): void;
	
	/**
	 * Interesting events. User logs in, SQL logs
	 *
	 * @param string $message
	 * @param array $context
	 * @param string|null $channel
	 */
	function info(string $message, array $context = [], ?string $channel = null): void;
	
	/**
	 * Detailed debug information
	 *
	 * @param string $message
	 * @param array $context
	 */
	function debug(string $message, array $context = []): void;
	
	/**
	 * Logs with an arbitrary level
	 *
	 * @param string $level
	 * @param string $message
	 * @param array $context
	 * @param string|null $channel
	 */
	function log(string $level, string $message, array $context = [], ?string $channel = null): void;
	
	/**
	 * @param array $context
	 */
	function setContext(array $context): void;

}