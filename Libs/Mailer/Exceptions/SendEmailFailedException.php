<?php
namespace Pandora3\Mailer\Exceptions;

use Pandora3\Contracts\ApplicationExceptionInterface;
use Pandora3\Mailer\Email;

class SendEmailFailedException extends \Exception implements ApplicationExceptionInterface {

	/** @var Email */
	protected $email;
	
	/**
	 * SendEmailFailedException constructor
	 * @param Email $email
	 * @param \Throwable|null $previous
	 */
	public function __construct(Email $email, ?\Throwable $previous = null) {
		parent::__construct('Failed to send email', E_ERROR, $previous);
		$this->email = $email;
	}
	
	/**
	 * @return Email
	 */
	public function getEmail(): Email {
		return $this->email;
	}
	
}