<?php

namespace {

	use Pandora3\Application\Application;

	if (!function_exists('env')) {
		/**
		 * @param string $key
		 * @param null $default
		 * @return mixed|null
		 */
		function env(string $key, $default = null) {
			return Application::instance()->getEnv($key, $default);
		}
	}

	if (!function_exists('route')) {
		/**
		 * @param string $routeName
		 * @param array ...$arguments
		 * @return string
		 */
		function route(string $routeName, ...$arguments): string {
			return Application::instance()->getRoutePath($routeName, $arguments);
		}
	}

}