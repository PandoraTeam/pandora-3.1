<?php
namespace Pandora3\Application;

use Pandora3\Contracts\SessionInterface;

/**
 * Class FlashMessages
 * @package Pandora3\Application
 */
class FlashMessages {
	
	public const Error		= 'error';
	public const Warning	= 'warning';
	public const Success	= 'success';
	public const Info		= 'info';
	
	/**
	 * @var SessionInterface
	 */
	protected $session;
	
	/**
	 * @var array
	 */
	protected $messages = [];
	
	/**
	 * FlashMessages constructor
	 * @param SessionInterface $session
	 */
	public function __construct(SessionInterface $session) {
		$this->session = $session;
		$messages = $session->get('_flashMessages');
		if ($messages) {
			$this->messages = $messages;
			$session->remove('_flashMessages');
		}
	}
	
	/**
	 * @param string $type
	 * @param string $message
	 */
	public function add(string $type, string $message): void {
		$flashMessages = $this->session->get('_flashMessages') ?? [];
		$flashMessages[] = ['type' => $type, 'message' => $message];
		$this->session->set('_flashMessages', $flashMessages);
	}

	/**
	 * Clear messages
	 */
	public function clear(): void {
		$this->session->remove('_flashMessages');
	}

	/**
	 * @return array
	 */
	public function getMessages(): array {
		return $this->messages;
	}
	
}