<?php
namespace Pandora3\Authentication;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Pandora3\Authentication\Exceptions\AuthTooManyLoginAttemptsException;

/**
 * Class ThrottleLogins
 * @package Pandora3\Authentication
 */
class ThrottleLogins {

	/** @var string */
	protected $loginAttemptsClassName;
	
	/** @var int */
	protected $maxAttempts;
	
	/** @var int */
	protected $decayMinutes;
	
	/**
	 * ThrottleLogins constructor
	 * @param array $config
	 * @param string $loginAttemptsClassName
	 */
	public function __construct(array $config = [], string $loginAttemptsClassName = '') {
		$this->loginAttemptsClassName = $loginAttemptsClassName;
		$this->maxAttempts = $config['maxAttempts'] ?? 0;
		$this->decayMinutes = $config['decayMinutes'] ?? 1;
	}
	
	/**
	 * @return Builder
	 */
	protected function query(): Builder {
		/** @var Model $loginAttemptsClassName */
		$loginAttemptsClassName = $this->loginAttemptsClassName;
		return $loginAttemptsClassName::query();
	}
	
	/**
	 * @param string $throttleKey
	 */
	public function incrementAttempts(string $throttleKey): void {
		if (!$this->maxAttempts || !$this->loginAttemptsClassName) {
			return;
		}
		
		$now = new \DateTimeImmutable();

		/** @var \EloquentModel $loginAttemptsClassName */
		$loginAttemptsClassName = $this->loginAttemptsClassName;
		$loginAttempts = $loginAttemptsClassName::firstOrCreate(
			['key' => $throttleKey], ['attempts' => 0, 'createTime' => $now]
		);
		
		if ($now >= $loginAttempts->createTime->modify("+{$this->decayMinutes} minutes")) {
			$loginAttempts->attempts = 0;
		}
		
		if ($loginAttempts->attempts >= $this->maxAttempts) {
			throw new AuthTooManyLoginAttemptsException();
		}

		$loginAttempts->attempts++;
		$loginAttempts->save();
	}
	
	/**
	 * @param string $throttleKey
	 */
	public function clearAttempts(string $throttleKey): void {
		if (!$this->maxAttempts || !$this->loginAttemptsClassName) {
			return;
		}
		$this->query()
			->where('key', $throttleKey)
			->delete();
	}
	
}