<?php
namespace Pandora3\Application;

use Pandora3\Application\Events\RedirectingEvent;
use Pandora3\Contracts\DispatcherInterface;
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
	
	protected const SessionKeyFlashMessages = 'flashMessages';
	
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
	 * @param DispatcherInterface $dispatcher
	 */
	public function __construct(SessionInterface $session, DispatcherInterface $dispatcher) {
		$this->session = $session;
		$messages = $session->get(self::SessionKeyFlashMessages);
		if ($messages) {
			$this->messages = $messages;
			$session->remove(self::SessionKeyFlashMessages);
		}

		$dispatcher->listen(RedirectingEvent::class, \Closure::fromCallable([$this, 'redirecting']));
	}
	
	/**
	 * @param string $type
	 * @param string $message
	 */
	public function add(string $type, string $message): void {
		$flashMessages = $this->session->get(self::SessionKeyFlashMessages) ?? [];
		$flashMessages[] = ['type' => $type, 'message' => $message];
		$this->session->set(self::SessionKeyFlashMessages, $flashMessages);
	}
	
	/**
	 * @param RedirectingEvent $event
	 */
	protected function redirecting(RedirectingEvent $event): void {
		$this->keepMessages();
	}
	
	/**
	 * Keep previous messages
	 */
	public function keepMessages(): void {
		if (!$this->messages) {
			return;
		}
		$flashMessages = $this->session->get(self::SessionKeyFlashMessages) ?? [];
		$this->session->set(self::SessionKeyFlashMessages, array_merge($flashMessages, $this->messages));
		$this->messages = [];
	}

	/**
	 * Clear messages
	 */
	public function clear(): void {
		$this->session->remove(self::SessionKeyFlashMessages);
	}

	/**
	 * @return array
	 */
	public function getMessages(): array {
		return $this->messages;
	}
	
}