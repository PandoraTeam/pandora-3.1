<?php
namespace Pandora3\Contracts;

/**
 * Interface UploadedFileFactoryInterface
 * @package Pandora3\Contracts
 */
interface UploadedFileFactoryInterface {
	
	/**
	 * @param array $file
	 * @return UploadedFileInterface|null
	 */
	function createUploadedFile(array $file): ?UploadedFileInterface;

}