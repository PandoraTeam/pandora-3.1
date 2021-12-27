<?php
namespace Pandora3\Session;
use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\SessionInterface;

/**
 * Class Session
 * @package Pandora3\Libs\Session
 */
class Session implements SessionInterface {

	/**
	 * @param bool $start
	 */
	public function __construct(bool $start = true) {
		if ($start) {
			$this->start();
		}
	}

	/**
	 * @param ContainerInterface $container
	 * @param int|null $lifetime
	 */
	public static function use(ContainerInterface $container, ?int $lifetime = null): void {
		$container->singleton(SessionInterface::class, function() use ($lifetime) {
			$session = new Session(false);
			$session->start($lifetime);
			return $session;
		});
	}
	
	/**
	 * @param int|null $lifeTime Session life time in seconds
	 */
	public function start(?int $lifeTime = null): void {
		session_set_cookie_params($lifeTime ?? 0);
		session_start();
	}
	
	/**
	 * Closes session
	 */
	public function close(): void {
		session_destroy();
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