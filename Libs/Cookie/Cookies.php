<?php
namespace Pandora3\Cookie;

class Cookies {

	/** @var Cookie[] $cookies */
	protected $cookies = [];

	/**
	 * @param Cookie $cookie
	 */
	public function set(Cookie $cookie): void {
		$this->cookies[$cookie->domain][$cookie->path][$cookie->name] = $cookie;
	}

	/**
	 * @param string $name
	 * @param string $path
	 * @param null|string $domain
	 */
	public function forget(
		string $name, string $path = '/', ?string $domain = null
	): void {
		$this->set(new Cookie($name, null, [
			'expire' => 1,
			'path' => $path,
			'domain' => $domain,
		]));
	}
	
	/**
	 * @return \Generator|Cookie[]
	 */
	public function getCookies(): \Generator {
		foreach ($this->cookies as $domainCookies) {
			foreach ($domainCookies as $pathCookies) {
				foreach ($pathCookies as $cookie) {
					yield $cookie;
				}
			}
		}
	}

}