<?php
namespace Pandora3\Contracts;

/**
 * Interface RouterInterface
 * @package Pandora3\Contracts
 */
interface RouterInterface {

	/**
	 * @param string $routeName
	 * @param array $arguments
	 * @return string
	 */
	function getRoutePath(string $routeName, array $arguments = []): string;

}