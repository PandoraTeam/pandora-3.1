<?php
namespace Pandora3\Permissions;

use Pandora3\Contracts\AuthenticationUserInterface;
use Pandora3\Contracts\PolicyInterface;

/**
 * Class Permissions
 * @package Pandora3\Permissions
 */
class Permissions {

	/** @var array */
	protected static $policyTypes = [];

	/** @var array */
	protected static $policies = [];
	
	public const GLOBAL_POLICY = ':GLOBAL';
	
	/**
	 * @param string $modelClass
	 * @param string $policyClass
	 */
	public static function registerPolicy(string $modelClass, string $policyClass): void {
		self::$policyTypes[$modelClass] = $policyClass;
	}
	
	/**
	 * @param string $policyClass
	 */
	public static function registerGlobalPolicy(string $policyClass): void {
		self::$policyTypes[self::GLOBAL_POLICY] = $policyClass;
	}

	/**
	 * @param array $policyTypes
	 */
	public static function registerPolicies(array $policyTypes): void {
		self::$policyTypes = array_replace(self::$policyTypes, $policyTypes);
	}

	/**
	 * @param string $className
	 * @return PolicyInterface
	 */
	protected static function getPolicy($className): PolicyInterface {
		if (!isset(self::$policies[$className])) {
			$policyClassName = self::$policyTypes[$className] ?? null;
			if (is_null($policyClassName)) {
				throw new \LogicException("Policy not registered for model [$className]");
			}
			if (!class_exists($policyClassName)) {
				throw new \LogicException("Policy class doesn't exist [$policyClassName]");
			}
			self::$policies[$className] = new $policyClassName();
		}
		return self::$policies[$className];
	}

	/**
	 * @param AuthenticationUserInterface $user
	 * @param string $action
	 * @param mixed|null $object
	 * @return bool
	 */
	public function can(AuthenticationUserInterface $user, string $action, $object = null): bool {
		if (substr($action, 0, 7) === 'global:') {
			$className = self::GLOBAL_POLICY;
			$action = substr($action, 7);
		} else {
			if (is_null($object)) {
				throw new \LogicException("Permissions::can 'object' argument is required");
			}
			$isModel = !is_string($object);
			$className = $isModel ? get_class($object) : $object;
		}
		
		$policy = self::getPolicy($className);
		if (!method_exists($policy, $action)) {
			$policyClassName = get_class($policy);
			throw new \LogicException("Policy [$policyClassName] method '$action' doesn't exist");
		}
		$allow = $policy->before($user, $action);
		if (!is_null($allow)) {
			return (bool) $allow;
		}
		return $isModel
			? $policy->$action($user, $object)
			: $policy->$action($user);
	}

}