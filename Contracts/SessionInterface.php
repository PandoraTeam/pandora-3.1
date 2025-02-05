<?php
namespace Pandora3\Contracts;

/**
 * Interface SessionInterface
 * @package Pandora3\Contracts
 */
interface SessionInterface {

	/**
	 * @param string $key
	 * @param mixed|null $default
	 * @return mixed|null
	 */
	function get(string $key, $default = null);

	/**
	 * @param string $key
	 * @param mixed|null $value
	 */
	function set(string $key, $value): void;

	/**
	 * @param string $key
	 * @return bool
	 */
	function has(string $key): bool;

	/**
	 * @param string $key
	 */
	function remove(string $key): void;
	
	/**
	 * @return string
	 */
	function getId(): string;
	
	/**
	 * @param string $id
	 */
	function setId(string $id): void;
	
	/**
	 * Start session
	 */
	function start(): void;
	
	/**
	 * Close session
	 */
	function close(): void;
	
	/**
	 * Write and close session
	 */
	function save(): void;

}