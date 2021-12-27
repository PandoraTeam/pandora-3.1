<?php
namespace Pandora3\Contracts;

/**
 * Interface RequestInterface
 * @package Pandora3\Contracts
 */
interface RequestInterface {

	/**
	 * @return string
	 */
	function getMethod(): string;
	
	/**
	 * @return string
	 */
	function getUri(): string;
	
	/**
	 * @return bool
	 */
	function isPost(): bool;

	/**
	 * @param string $key
	 * @return mixed|null
	 */
	function get(string $key);

	/**
	 * @param string $key
	 * @return mixed|null
	 */
	function post(string $key);

	/**
	 * @param string $key
	 * @return mixed|null
	 */
	function file(string $key);

	/**
	 * @param string $key
	 * @return mixed|null
	 */
	function cookie(string $key);
	
	/**
	 * @param string $key
	 * @return mixed|null
	 */
	function getAttribute(string $key);

	/**
	 * @param null|string $method
	 * @return array
	 */
	function all(?string $method = null): array;
	
	/**
	 * @return array
	 */
	function files(): array;

	/**
	 * @return array
	 */
	function cookies(): array;

	/**
	 * @return array
	 */
	function server(): array;

	/**
	 * @return array
	 */
	function getValues(): array;

	/**
	 * @return array
	 */
	function postValues(): array;

	/**
	 * @return array
	 */
	function attributes(): array;
	
}