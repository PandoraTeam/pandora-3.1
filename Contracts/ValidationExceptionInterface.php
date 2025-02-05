<?php
namespace Pandora3\Contracts;

/**
 * Interface ValidationExceptionInterface
 * @package Pandora3\Contracts
 */
interface ValidationExceptionInterface extends ApplicationLogicExceptionInterface {

	function getMessages(): array;
	
	function getFormName(): ?string;

}