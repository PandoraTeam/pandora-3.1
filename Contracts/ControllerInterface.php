<?php
namespace Pandora3\Contracts;

/**
 * Interface ControllerInterface
 * @package Pandora3\Contracts
 */
interface ControllerInterface {
	
	/**
	 * @return array
	 */
	function middlewares(): array;

	/**
	 * @param string $methodName
	 * @param RequestInterface $request
	 * @param array ...$arguments
	 * @return ResponseInterface
	 */
	function handleAction(string $methodName, RequestInterface $request, ...$arguments): ResponseInterface;

}