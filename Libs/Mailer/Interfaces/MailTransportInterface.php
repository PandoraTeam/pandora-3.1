<?php
namespace Pandora3\Mailer\Interfaces;

use Pandora3\Mailer\Email;
use Pandora3\Mailer\Exceptions\SendEmailFailedException;

/**
 * Interface MailTransportInterface
 * @package Pandora3\Mailer\Interfaces
 */
interface MailTransportInterface {

	/**
	 * @param Email $email
	 * @throws SendEmailFailedException
	 */
	function send(Email $email): void;
	
	/**
	 * @return string
	 */
	function getSentMIMEMessage(): string;

}