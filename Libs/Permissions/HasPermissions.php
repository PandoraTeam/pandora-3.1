<?php
namespace Pandora3\Permissions;

use Pandora3\Contracts\AuthenticationUserInterface;

/**
 * Trait UserPermissions
 * @package Pandora3\Permissions
 * @mixin AuthenticationUserInterface
 */
trait HasPermissions {

	/**
	 * @param string $action
	 * @param mixed $object
	 * @return bool
	 */
	public function can(string $action, $object): bool {
		return \Gate::permissions()->can($this, $action, $object);
	}

}