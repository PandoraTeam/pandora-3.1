<?php
namespace Pandora3\Mailer;

class Attachment {

	/** @var string */
	public $path;

	/** @var string */
	public $content;

	/** @var string */
	public $name;

	/** @var string */
	public $mimeType;

	/** @var string */
	public $contentId;

	/** @var string */
	public $disposition;

	/**
	 * @param string $path
	 * @return static
	 */
	public function path(string $path): self {
		$this->path = $path;
		return $this;
	}

	/**
	 * @param string $content
	 * @return static
	 */
	public function content(string $content): self {
		$this->content = $content;
		return $this;
	}

	/**
	 * @param string $mimeType
	 * @return static
	 */
	public function mimeType(string $mimeType): self {
		$this->mimeType = $mimeType;
		return $this;
	}

	// todo: implement 'disposition'

	// todo: implement 'contentId'

	// todo: implement 'name'

}