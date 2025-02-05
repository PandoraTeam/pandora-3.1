<?php
namespace Pandora3\Authentication;

use Pandora3\Authentication\Exceptions\AuthUserNotFoundException;
use Pandora3\Authentication\Exceptions\AuthWrongPasswordException;
use Pandora3\Contracts\AuthenticationUserInterface;
use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\SessionInterface;
use Pandora3\Contracts\UserProviderInterface;
use Pandora3\Http\Request;

/**
 * Class Authentication
 * @package Pandora3\Authentication
 */
class Authentication {

	protected const SessionKeyUserId = 'authenticationUserId';
	
	protected const SessionKeyReturnUri = 'authenticationReturnUri';

	/** @var SessionInterface */
	protected $session;
	
	/** @var UserProviderInterface */
	protected $userProvider;
	
	/** @var AuthenticationUserInterface|null */
	protected $user;
	
	/** @var int|null */
	protected $userId;
	
	public function __construct(SessionInterface $session, UserProviderInterface $userProvider) {
		$this->session = $session;
		$this->userProvider = $userProvider;
	}

	/**
	 * @param ContainerInterface $container
	 */
	public static function use(ContainerInterface $container): void {
		$container->singleton(Authentication::class);
	}

	/**
	 * @param AuthenticationUserInterface $user
	 */
	public function authenticateUser(AuthenticationUserInterface $user): void {
		$this->user = $user;
		$this->userId = $user->getAuthenticationId();
		$this->session->set(self::SessionKeyUserId, $this->userId);
	}
	
	/**
	 * Cleans user authentication
	 */
	public function signOut(): void {
		$this->user = null;
		$this->userId = 0;
		$this->session->remove(self::SessionKeyUserId);
	}
	
	/**
	 * @param string $login
	 * @param string $password
	 * @return AuthenticationUserInterface|null
	 * @throws AuthUserNotFoundException
	 * @throws AuthWrongPasswordException
	 */
	public function authenticate(string $login, string $password): ?AuthenticationUserInterface {
		$user = $this->userProvider->getUserByLogin($login);
		if (!$user) {
			throw new AuthUserNotFoundException($login);
		}
		if (!$user->checkPassword($password)) {
			throw new AuthWrongPasswordException();
		}
		$this->authenticateUser($user);
		return $user;
	}

	/**
	 * @return AuthenticationUserInterface|null
	 */
	public function getUser(): ?AuthenticationUserInterface {
		if (is_null($this->userId)) {
			$this->userId = $this->session->get(self::SessionKeyUserId, 0);
		}
		if ($this->userId && is_null($this->user)) {
			$this->user = $this->userProvider->getUserById($this->userId);
			// todo: should we do something like this?
			// if (!$this->user) { $this->signOut(); }
		}
		return $this->user;
	}
	
	/**
	 * @return int|null
	 */
	public function getUserId(): ?int {
		if (is_null($this->userId)) {
			$this->userId = $this->session->get(self::SessionKeyUserId, 0);
		}
		return $this->userId ?: null;
	}
	
	/**
	 * @param null|string $returnUri
	 */
	public function setReturnUri(?string $returnUri): void {
		$this->session->set(self::SessionKeyReturnUri, $returnUri);
	}
	
	/**
	 * @param RequestInterface $request
	 */
	public function setReturnUriFromRequest(RequestInterface $request): void {
		if ($request->getMethod() === Request::METHOD_GET) {
			$params = http_build_query($request->getValues());
			if ($params) {
				$params = '?' . $params;
			}
			$this->setReturnUri($request->getUri() . $params);
		}
	}
	
	/**
	 * @param bool $removeSession
	 * @return null|string
	 */
	public function getReturnUri(bool $removeSession = false): ?string {
		$returnUri = $this->session->get(self::SessionKeyReturnUri);
		if ($removeSession && !is_null($returnUri)) {
			$this->session->remove(self::SessionKeyReturnUri);
		}
		return $returnUri;
	}

}