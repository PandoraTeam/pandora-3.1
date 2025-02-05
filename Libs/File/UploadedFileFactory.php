<?php
namespace Pandora3\File;

use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\UploadedFileFactoryInterface;
use Pandora3\Contracts\UploadedFileInterface;

class UploadedFileFactory implements UploadedFileFactoryInterface {
	
	/**
	 * @param ContainerInterface $container
	 */
	public static function use(ContainerInterface $container) {
		$container->singleton(UploadedFileFactoryInterface::class, UploadedFileFactory::class);
	}
	
	/**
	 * @param array $file
	 * @return UploadedFileInterface|null
	 */
	public function createUploadedFile(array $file): ?UploadedFileInterface {
		$error = $file['error'] ?? null;
		if ($error === UPLOAD_ERR_NO_FILE) {
			return null;
		}
		if (empty($file['tmp_name'])) {
			throw new \RuntimeException('Uploaded file "tmp_name" is missing');
		}
		if (empty($file['name'])) {
			throw new \RuntimeException('Uploaded file "name" is missing');
		}
		return new UploadedFile(
			$file['tmp_name'],
			$file['name'],
			$file['type'] ?? null,
			$file['size'] ?? null,
			$error
		);
	}
	
}