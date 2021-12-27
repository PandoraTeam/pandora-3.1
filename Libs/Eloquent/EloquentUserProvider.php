<?php
namespace Pandora3\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Pandora3\Contracts\AuthenticationUserInterface;
use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\UserProviderInterface;

/**
 * Class EloquentUserProvider
 * @package Pandora3\Plugins\Eloquent
 */
class EloquentUserProvider implements UserProviderInterface {

	/** @var string */
	protected $userClassName;
	
	/**
	 * @param string $userClassName
	 */
	public function __construct(string $userClassName) {
		$this->userClassName = $userClassName;
	}
	
	/**
	 * @param ContainerInterface $container
	 * @param string $userClassName
	 */
	public static function use(ContainerInterface $container, string $userClassName): void {
		$container->singleton(UserProviderInterface::class, function() use ($userClassName) {
			return new EloquentUserProvider($userClassName);
		});
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getUserById($id): ?AuthenticationUserInterface {
		/** @var Model $userClassName */
		$userClassName = $this->userClassName;

		/** @var AuthenticationUserInterface $user */
		$user = $userClassName::query()->find($id);
		return $user;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUserByLogin(string $login): ?AuthenticationUserInterface {
		/** @var Model $userClassName */
		$userClassName = $this->userClassName;

		/** @var AuthenticationUserInterface $user */
		$user = $userClassName::query()->where(['login' => $login])->first();
		return $user;
	}

}