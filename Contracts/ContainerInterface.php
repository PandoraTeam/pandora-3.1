<?php
namespace Pandora3\Contracts;

/**
 * Interface ContainerInterface
 * @package Pandora3\Contracts
 */
interface ContainerInterface {

	/**
	 * @param string $abstract
	 * @param string|\Closure $dependency
	 */
	function bind(string $abstract, $dependency): void;

	/**
	 * @param string $abstract
	 * @param string|\Closure|null $dependency
	 */
	function singleton(string $abstract, $dependency = null): void;

	/**
	 * Create or get instance by class or interface
	 * @param string $abstract
	 * @param array $params
	 * @return mixed
	 */
	function make(string $abstract, array $params = []): object;
	
	/**
	 * Create instance of a class
	 * @param string $className
	 * @param array $params
	 * @return mixed
	 */
	function build(string $className, array $params = []): object;

	/**
	 * @param string $key
	 * @return object
	 */
	function get(string $key): object;

	/**
	 * @param string $key
	 * @return bool
	 */
	function has(string $key): bool;

}