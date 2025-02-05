<?php
namespace Pandora3\Contracts;

/**
 * Interface RendererInterface
 * @package Pandora3\Contracts
 */
interface RendererInterface {

	/**
	 * @param string $viewPath
	 * @param array $context
	 * @return string
	 * @throws \RuntimeException
	 */
	function render(string $viewPath, array $context = []): string;

}