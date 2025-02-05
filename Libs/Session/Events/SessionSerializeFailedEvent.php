<?php
namespace Pandora3\Session\Events;

use Pandora3\Contracts\SessionInterface;

/**
 * Class SessionSerializeFailedEvent
 * @package Pandora3\Application
 */
class SessionSerializeFailedEvent {

	/** @var SessionInterface */
	protected $session;
	
	/**
	 * RedirectingEvent constructor
	 * @param SessionInterface $session
	 */
	public function __construct(SessionInterface $session) {
		$this->session = $session;
	}
	
}