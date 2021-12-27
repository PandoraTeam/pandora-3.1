<?php
namespace Pandora3\Contracts;

/**
 * Interface RequestFactoryInterface
 * @package Pandora3\Contracts
 */
interface RequestFactoryInterface {

	/**
	 * @return RequestInterface
	 */
	function createRequest(): RequestInterface;

}