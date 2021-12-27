<?php
namespace Pandora3\Http;

use Pandora3\Contracts\UriInterface;

/**
 * Class Uri
 * @package Pandora3\Http
 */
class Uri implements UriInterface {
	
	/** @var string */
	protected $uri;
	
	/**
	 * Uri constructor
	 * @param string $uri
	 */
	public function __construct(string $uri) {
		$this->uri = $uri;
	}
	
	/**
	 * @return string
	 */
	public function getUri(): string {
		return $this->uri;
	}
	
}