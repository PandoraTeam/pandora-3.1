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
		$container->singleton(StorageInterface::class, static function() use ($config) {
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
	 * @param UploadedFileInterface|FileInterface $file
	 * @param string $path
	 * @return string
	 */
	public function uploadFile($file, string $path): string {
        try {
            $fileName = $this->uniqueFileName($file->getExtension(), $path);
            if (!$this->upload($file, $path . '/' . $fileName)) {
                throw new \Exception('Could not upload file');
            }
            return $fileName;
        } catch (\Exception $ex) {
            throw new \LogicException("Failed to move uploaded file '{$file->getPath()}'", E_ERROR, $ex);
        }
    }

	/**
	 * {@inheritdoc}
	 */
	public function delete(string $path): bool {
		return unlink($this->path . '/' . $path);
	}
	
	/**
	 * @param string $extension
	 * @param string $path
	 * @return string
	 */
    public function uniqueFileName(string $extension, string $path): string {
    	try {
			$path = $this->path.'/'.$path.'/';
			do {
				$fileName = bin2hex(random_bytes(8)).($extension ? '.'.$extension : '');
			} while (file_exists($path.$fileName));
			return $fileName;
		} catch (\Exception $ex) {
			throw new \LogicException("Failed to generate unique file name", E_ERROR, $ex);
		}
    }

	/* public function isDirectory(string $path): bool {
	} */

	/* public function url(string $path): string {
	} */

}