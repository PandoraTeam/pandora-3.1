<?php
namespace Pandora3\File;

use Pandora3\Contracts\FileInterface;
use Pandora3\Contracts\UploadedFileInterface;

/**
 * Class UploadedFile
 * @package Pandora3\File
 */
class UploadedFile implements UploadedFileInterface {

	/** @var string */
	protected $path;

	/** @var string */
	protected $uploadName;

	/** @var string|null */
	protected $mediaType;

	/** @var int|null */
	protected $size;

	/** @var int|null */
	protected $error;
	
	/**
	 * UploadedFile constructor
	 * @param string $path
	 * @param string $uploadName
	 * @param string|null $mediaType
	 * @param int|null $size
	 * @param int|null $error
	 */
	public function __construct(
		string $path, string $uploadName,
		?string $mediaType = null, ?int $size = null,
		?int $error = UPLOAD_ERR_OK
	) {
		$this->path = $path;
		$this->uploadName = $uploadName;
		$this->mediaType = $mediaType;
		$this->size = $size;
		$this->error = $error;
	}

	/**
	 * @param string $directory
	 * @param string|null $name
	 * @return File
	 */
	public function move(string $directory, ?string $name = null): FileInterface {
		if (!is_uploaded_file($this->path)) {
			throw new \RuntimeException("File is not a valid uploaded file '{$this->path}'");
		}

		if (is_null($name)) {
			$name = pathinfo($this->path, PATHINFO_FILENAME);
		}
		$targetPath = $directory . '/' . $name;
		FileUtils::createPath($directory);
		if (!move_uploaded_file($this->path, $targetPath)) {
			throw new \RuntimeException("Failed to move file to '$targetPath'");
		}

		return new File($targetPath);
	}

	/**
	 * @return string
	 */
	public function getPath(): string {
		return $this->path;
	}

	/**
	 * Get temporary file name
	 * @return string
	 */
	public function getName(): string {
		return FileUtils::getFileName($this->path);
		// $extension = FileUtils::getFileExtension($this->path);
		// return FileUtils::getFileName($this->path).($extension ? '.'.$extension : '');
	}
	
	/**
	 * Get temporary file name without extension (same as getName)
	 * @return string
	 */
	public function getNameWithoutExtension(): string {
		return FileUtils::getFileName($this->path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMediaType(): ?string {
		return $this->mediaType;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSize(): ?int {
		return $this->size;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getError(): ?int {
		return $this->error;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUploadName(): string {
		return $this->uploadName;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUploadNameWithoutExtension(): string {
		return FileUtils::getFileName($this->uploadName);
	}

	/**
	 * Get uploaded file extension
	 * {@inheritdoc}
	 */
	public function getExtension(): string {
		return FileUtils::getFileExtension($this->uploadName);
	}

}