<?php
namespace Pandora3\File;

use Pandora3\Contracts\FileInterface;

/**
 * Class File
 * @package Pandora3\File
 */
class File implements FileInterface {

	/** @var string */
	protected $path;
	
	/** @var string|null */
	protected $uploadName;
	
	/**
	 * File constructor
	 * @param string $path
	 * @param string|null $uploadName
	 */
	public function __construct(string $path, ?string $uploadName = null) {
		$this->path = $path;
		$this->uploadName = $uploadName;
	}

	// todo: implement
	/* public function move(string $directory, ?string $name = null): FileInterface {
	} */
	
	/**
	 * {@inheritdoc}
	 */
	public function getSize(): ?int {
		return FileUtils::getFileSize($this->path);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getPath(): string {
		return $this->path;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getName(): string {
		$extension = FileUtils::getFileExtension($this->path);
		return FileUtils::getFileName($this->path).($extension ? '.'.$extension : '');
	}
	
	/**
	 * {@inheritdoc}
	 */
	function getUploadName(): string {
		return $this->uploadName ?? $this->getName();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getNameWithoutExtension(): string {
		return FileUtils::getFileName($this->path);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getExtension(): string {
		return FileUtils::getFileExtension($this->path);
	}

}