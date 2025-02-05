<?php
namespace Pandora3\Impersonate;

use Pandora3\Authentication\Authentication;
use Pandora3\Contracts\AuthenticationUserInterface;
use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\SessionInterface;
use Pandora3\Contracts\UserProviderInterface;

/**
 * Class Impersonate
 * @package Pandora3\Libs\Impersonate
 */
class Impersonate {

	protected const SessionKeyImpersonatorUserId = 'authenticationImpersonatorUserId';

	/** @var SessionInterface */
	protected $session;
	
	/** @var Authentication */
	protected $authentication;
	
	/** @var UserProviderInterface */
	protected $userProvider;

	/**
	 * @param SessionInterface $session
	 * @param Authentication $authentication
	 * @param UserProviderInterface $userProvider
	 */
	public function __construct(SessionInterface $session, Authentication $authentication, UserProviderInterface $userProvider) {
		$this->session = $session;
		$this->authentication = $authentication;
		$this->userProvider = $userProvider;
	}

	/**
	 * @param ContainerInterface $container
	 */
	public static function use(ContainerInterface $container): void {
		$container->singleton(Impersonate::class);
	}

	/**
	 * @param AuthenticationUserInterface $user
	 * @return bool
	 */
	public function impersonate(AuthenticationUserInterface $user): bool {
		$impersonatorUser = $this->getImpersonator() ?? $this->authentication->getUser();
		if (!$impersonatorUser) {
			return false;
		}
		$this->authentication->signOut();
		$this->authentication->authenticateUser($user);
		$this->session->set(self::SessionKeyImpersonatorUserId, $impersonatorUser->getAuthenticationId());
		return true;
	}

	/**
	 * @return null|AuthenticationUserInterface
	 */
	public function getImpersonator(): ?AuthenticationUserInterface {
		$impersonatorUserId = $this->session->get(self::SessionKeyImpersonatorUserId, 0);
		if ($impersonatorUserId) {
			return $this->userProvider->getUserById($impersonatorUserId);
		}
		return null;
	}

	/**
	 * @return bool
	 */
	public function isImpersonating(): bool {
		$impersonatorUserId = $this->session->get(self::SessionKeyImpersonatorUserId, 0);
		return (bool) $impersonatorUserId;
	}

	/**
	 * @return bool
	 */
	public function leaveImpersonation(): bool {
		$impersonatorUserId = $this->session->get(self::SessionKeyImpersonatorUserId, 0);
		if (!$impersonatorUserId) {
			return false;
		}
		$this->session->remove(self::SessionKeyImpersonatorUserId);
		$this->authentication->signOut();
		$impersonatorUser = $this->userProvider->getUserById($impersonatorUserId);
		if ($impersonatorUser) {
			$this->authentication->authenticateUser($impersonatorUser);
		}
		return true;
	}

	/**
	 * Leave impersonation after sing out
	 */
	public function afterSignOut(): void {
		// todo: think different way, without the need to manually call of this function
		$this->session->remove(self::SessionKeyImpersonatorUserId);
	}

}