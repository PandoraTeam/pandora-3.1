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
	
	/**
	 * File constructor
	 * @param string $path
	 */
	public function __construct(string $path) {
		$this->path = $path;
	}

	// todo: implement
	/* public function move(string $directory, ?string $name = null) {
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