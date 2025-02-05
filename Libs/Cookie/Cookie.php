<?php
namespace Pandora3\Cookie;

/**
 * Class Cookie
 * @package Pandora3\Cookie
 */
class Cookie {

	/** @var string $name */
	public $name;

	/** @var string|null $value */
	public $value;

	/** @var int $expire */
	public $expire;

	/** @var string $path */
	public $path;

	/** @var string|null $domain */
	public $domain;

	/** @var bool $isSecure */
	public $isSecure;

	/** @var bool $isHttpOnly */
	public $isHttpOnly;

	/** @var string|null $sameSite */
	public $sameSite;

	const SAME_SITE_LAX = 'Lax';
	const SAME_SITE_STRICT = 'Strict';

	public function __construct(string $name, ?string $value = null, array $params = []) {
		if (!$name) {
			throw new \LogicException('Cookie name is empty');
		}

		if (preg_match("#[=,;\s\t\r\n\013\014]#", $name)) {
			throw new \LogicException("Cookie name '$name' contains invalid characters");
		}

		$sameSite = $params['sameSite'] ?? null;
		if (!\in_array($sameSite, [self::SAME_SITE_LAX, self::SAME_SITE_STRICT, null], true)) {
			throw new \LogicException("Cookie parameter 'sameSite' has wrong value");
		}

		$this->name = $name;
		$this->value = $value;

		$expire = $params['expire'] ?? 0;
		if ($expire instanceof \DateTimeInterface) {
			$expire = $expire->getTimestamp();
		} else if (!is_numeric($expire)) {
			$expire = strtotime($expire);
			if ($expire === false) {
				throw new \LogicException("Cookie parameter 'expire' has wrong value");
			}
		}
		$this->expire = ($expire > 0)
			? (int) $expire
			: 0;

		$this->path = $params['path'] ?? '/';
		$this->domain = $params['domain'] ?? null;
		$this->isSecure = $params['isSecure'] ?? false;
		$this->isHttpOnly = $params['isHttpOnly'] ?? true;
		$this->sameSite = $sameSite;
	}

	public function getMaxAge(): int {
		$maxAge = $this->expire - time();
		return ($maxAge > 0) ? $maxAge : 0;
	}

	public function __toString(): string {
		$result = "{$this->name}=";

		$value = $this->value;
		if (is_null($value) || $value === '') {
			$expire = gmdate('D, d-M-Y H:i:s T', time() - 31536001); // 'Thu, 01 Jan 1970 00:00:00 GMT';
			$result .= "deleted; Expires={$expire}; Max-Age=0";
		} else {
			$result .= rawurlencode($value);
			if ($this->expire > 0) {
				$expire = gmdate('D, d-M-Y H:i:s T', $this->expire);
				$maxAge = $this->getMaxAge();
				$result .= "; Expires={$expire}; Max-Age={$maxAge}";
			}
		}

		if ($this->path) {
			$result .= "; Path={$this->path}";
		}

		if ($this->domain) {
			$result .= "; Domain={$this->domain}";
		}

		if ($this->isSecure) {
			$result .= '; Secure';
		}

		if ($this->isHttpOnly) {
			$result .= '; HttpOnly';
		}

		if ($this->sameSite) {
			$result .= "; SameSite={$this->sameSite}";
		}

		return $result;
	}

}