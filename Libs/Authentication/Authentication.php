<?php
namespace Pandora3\Authentication;
use Pandora3\Authentication\Exceptions\AuthUserNotFoundException;
use Pandora3\Authentication\Exceptions\AuthWrongPasswordException;
use Pandora3\Contracts\AuthenticationUserInterface;
use Pandora3\Contracts\SessionInterface;
use Pandora3\Contracts\UserProviderInterface;

/**
 * Class Authentication
 * @package Pandora3\Libs\Authentication
 */
class Authentication {

	const AuthenticationSessionKey = 'authenticationUserId';

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
	 * @param AuthenticationUserInterface $user
	 */
	public function authenticateUser(AuthenticationUserInterface $user): void {
		$this->user = $user;
		$this->userId = $user->getAuthenticationId();
		$this->session->set(self::AuthenticationSessionKey, $this->userId);
	}
	
	/**
	 * Cleans user authentication
	 */
	public function signOut(): void {
		$this->user = null;
		$this->userId = 0;
		$this->session->remove(self::AuthenticationSessionKey);
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
			$this->userId = $this->session->get(self::AuthenticationSessionKey, 0);
			if ($this->userId) {
				$this->user = $this->userProvider->getUserById($this->userId);
			}
		}
		return $this->user;
	}

}