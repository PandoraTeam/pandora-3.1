<?php
namespace Pandora3\Contracts;

/**
 * Interface UploadedFileFactoryInterface
 * @package Pandora3\Contracts
 */
interface UploadedFileFactoryInterface {
	
	/**
	 * @param array $file
	 * @return mixed
	 */
	function createUploadedFile(array $file);

}