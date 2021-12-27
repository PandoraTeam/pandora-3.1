<?php
namespace Pandora3\Contracts;

/**
 * Interface RequestHandlerInterface
 * @package Pandora3\Contracts
 */
interface RequestHandlerInterface {

	/**
	 * @param RequestInterface $request
	 * @return ResponseInterface
	 */
	function handleRequest(RequestInterface $request): ResponseInterface;

}