<?php
namespace Pandora3\Application\Events;

use Pandora3\Contracts\UriInterface;

/**
 * Class RedirectingEvent
 * @package Pandora3\Application
 */
class RedirectingEvent {

	/** @var string|UriInterface */
	protected $uri;
	
	/** @var int */
	protected $code;
	
	/**
	 * RedirectingEvent constructor
	 * @param string|UriInterface $uri
	 * @param int $code
	 */
	public function __construct($uri, int $code) {
		$this->uri = $uri;
		$this->code = $code;
	}
	
}