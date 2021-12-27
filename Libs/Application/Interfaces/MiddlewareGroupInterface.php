<?php
namespace Pandora3\Application\Interfaces;

use Pandora3\Contracts\MiddlewareInterface;

/**
 * Interface MiddlewareGroup
 * @package Pandora3\Application\Interfaces
 */
interface MiddlewareGroupInterface {

	/**
	 * @return MiddlewareInterface[]
	 */
	function getMiddlewares(): array;

}