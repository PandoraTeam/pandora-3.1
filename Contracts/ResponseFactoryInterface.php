<?php
namespace Pandora3\Contracts;

/**
 * Interface ResponseFactoryInterface
 * @package Pandora3\Contracts
 */
interface ResponseFactoryInterface {

	/**
	 * @param string $content
	 * @param int $code
	 * @param array $headers
	 * @return ResponseInterface
	 */
	function createResponse(string $content, int $code, array $headers = []): ResponseInterface;

}