<?php
namespace Pandora3\Session;

use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\DispatcherInterface;
use Pandora3\Contracts\SessionInterface;
use Pandora3\Session\Events\SessionSerializeFailedEvent;

/**
 * Class Session
 * @package Pandora3\Session
 */
class Session implements SessionInterface {

	/** @var DispatcherInterface|null */
	protected $dispatcher = null;

	/**
	 * @param DispatcherInterface|null $dispatcher
	 * @param bool $start
	 */
	public function __construct(?DispatcherInterface $dispatcher = null, bool $start = true) {
		$this->dispatcher = $dispatcher;
		if ($start) {
			$this->start();
		}
	}

	/**
	 * @param ContainerInterface $container
	 * @param int|null $lifetime
	 */
	public static function use(ContainerInterface $container, ?int $lifetime = null): void {
		$container->singleton(SessionInterface::class, static function() use ($lifetime, $container) {
			/** @var Session $session */
			$session = $container->build(Session::class, ['start' => false]);
			$session->start($lifetime);
			return $session;
		});
	}
	
	/**
	 * Start session
	 * @param int|null $lifeTime Session life time in seconds
	 */
	public function start(?int $lifeTime = null): void {
		session_set_cookie_params($lifeTime ?? 0);
		session_start();
	}
	
	/**
	 * Destroy session
	 */
	public function close(): void {
		session_destroy();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function save(): void {
		$sessionData = $_SESSION;
		try {
			session_write_close();
		} catch (\Throwable $exception) {
			if (is_null($this->dispatcher)) {
				throw new \RuntimeException('Failed to serialize session', E_USER_ERROR, $exception);
			}
			ob_start();
				// >> this session start should fail
				set_error_handler(null);
				session_start();
				restore_error_handler();
				// <<
				session_start();
			ob_end_flush();
			$_SESSION = $sessionData;
			$this->dispatcher->dispatch(new SessionSerializeFailedEvent($this));
			try {
				// retying to save the session
				ob_start();
				session_write_close();
			} catch (\Throwable $exception) {
				throw new \RuntimeException('Failed to serialize session', E_USER_ERROR, $exception);
			} finally {
				ob_end_flush();
			}
		}
	}
	
	/**
	 * Regenerates session id
	 */
	public function regenerate(): void {
		session_regenerate_id();
	}

	/**
	 * @param string $id
	 */
	public function setId(string $id): void {
		session_id($id);
	}

	/**
	 * @return string
	 */
	public function getId(): string {
		return session_id();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get(string $key, $default = null) {
		return $_SESSION[$key] ?? $default;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set(string $key, $value): void {
		$_SESSION[$key] = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function has(string $key): bool {
		return isset($_SESSION[$key]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove(string $key): void {
		unset($_SESSION[$key]);
	}

}