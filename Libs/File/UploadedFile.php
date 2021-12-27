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
	protected $name;

	/** @var string|null */
	protected $mediaType;

	/** @var int|null */
	protected $size;

	/** @var int|null */
	protected $error;
	
	/**
	 * UploadedFile constructor
	 * @param string $path
	 * @param string $name
	 * @param null|string $mediaType
	 * @param int|null $size
	 * @param int|null $error
	 */
	public function __construct(
		string $path, string $name,
		?string $mediaType = null, ?int $size = null,
		?int $error = UPLOAD_ERR_OK
	) {
		$this->path = $path;
		$this->name = $name;
		$this->mediaType = $mediaType;
		$this->size = $size;
		$this->error = $error;
	}

	/**
	 * @param string $directory
	 * @param null|string $name
	 * @return File
	 */
	function move(string $directory, ?string $name = null): FileInterface {
		if (!is_uploaded_file($this->path)) {
			throw new \RuntimeException("File is not a valid uploaded file '{$this->path}'");
		}

		if (is_null($name)) {
			$name = pathinfo($this->path, PATHINFO_FILENAME);
		}
		$targetPath = $directory . '/' . $name;
		FileUtils::createPath($directory);
		if (!move_uploaded_file($this->path, $targetPath)) {
			throw new \RuntimeException("Failed to move file to '$targetPath' ");
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
	 * @return string
	 */
	public function getUploadName(): string {
		return $this->name;
	}

	/**
	 * @return null|string
	 */
	public function getMediaType(): ?string {
		return $this->mediaType;
	}

	/**
	 * @return int|null
	 */
	public function getSize(): ?int {
		return $this->size;
	}

	/**
	 * @return int|null
	 */
	public function getError(): ?int {
		return $this->error;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		$extension = FileUtils::getFileExtension($this->path);
		return FileUtils::getFileName($this->path).($extension ? '.'.$extension : '');
	}

	/**
	 * @return string
	 */
	public function getNameWithoutExtension(): string {
		return FileUtils::getFileName($this->name);
	}

	/**
	 * @return string
	 */
	public function getExtension(): string {
		return FileUtils::getFileExtension($this->name);
	}

}