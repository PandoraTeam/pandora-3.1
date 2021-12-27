<?php
namespace Pandora3\File;

use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\FileInterface;
use Pandora3\Contracts\StorageInterface;
use Pandora3\Contracts\UploadedFileInterface;

/**
 * Class Storage
 * @package Pandora3\File
 */
class Storage implements StorageInterface {

	/** @var string */
	protected $path;
	
	/**
	 * Storage constructor
	 * @param array $config
	 */
	public function __construct(array $config) {
		if (empty($config['path'])) {
			throw new \RuntimeException("Config 'path' is not defined");
		}
		$this->path = $config['path'];
	}
	
	/**
	 * @param ContainerInterface $container
	 * @param array $config
	 */
	public static function use(ContainerInterface $container, array $config): void {
		$container->singleton(StorageInterface::class, function() use ($config) {
			return new Storage($config);
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPath(): string {
		return $this->path;
	}

	/**
	 * @param UploadedFileInterface|FileInterface|string $file
	 * @param string $path
	 * @param bool $move
	 * @return bool
	 */
	public function upload($file, string $path, bool $move = true): bool {
		$targetPath = $this->path . '/' . $path;
		FileUtils::createPath(dirname($targetPath));
		if ($file instanceof UploadedFileInterface) {
			return move_uploaded_file($file->getPath(), $targetPath);
		}
		$localPath = ($file instanceof FileInterface) ? $file->getPath() : $file;
		return $move
			? rename($localPath, $targetPath)
			: copy($localPath, $targetPath);
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete(string $path): bool {
		return unlink($this->path . '/' . $path);
	}

	/* public function isDirectory(string $path): bool {
	} */

	/* public function url(string $path): string {
	} */

}