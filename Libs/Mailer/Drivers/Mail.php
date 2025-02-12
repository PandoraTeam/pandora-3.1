<?php
namespace Pandora3\Mailer\Drivers;

use Pandora3\Mailer\Email;
use Pandora3\Mailer\Exceptions\SendEmailFailedException;
use Pandora3\Mailer\Interfaces\MailTransportInterface;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Class Mail
 * @package Pandora3\Mailer\Drivers
 */
class Mail implements MailTransportInterface {

	/** @var PHPMailer */
	protected $mailer;

	/**
	 * @param array $options
	 */
	public function __construct(array $options = []) {
		$mailer = new PHPMailer(true);
		$charset = $options['charset'] ?? 'utf-8';
		if ($charset === 'utf8') {
			$charset = 'utf-8';
		}
		$mailer->CharSet = $charset;
		if (isset($options['timeout'])) {
			$mailer->Timeout = $options['timeout'];
		}
		$this->mailer = $mailer;
	}
	
	/**
	 * @param Email $email
	 * @throws SendEmailFailedException
	 */
	public function send(Email $email): void {
		try {
			foreach ($email->to as $to) {
				$this->mailer->addAddress($to['email'], $to['name']);
			}
			foreach ($email->replyTo as $replyTo) {
				$this->mailer->addReplyTo($replyTo['email'], $replyTo['name']);
			}
			foreach ($email->cc as $cc) {
				$this->mailer->addCC($cc['email'], $cc['name']);
			}
			foreach ($email->bcc as $bcc) {
				$this->mailer->addBCC($bcc['email'], $bcc['name']);
			}
			$this->mailer->setFrom($email->from['email'], $email->from['name']);
			$this->mailer->Subject = $email->subject;
	
			if ($email->htmlBody) {
				$this->mailer->isHTML(true);
				$this->mailer->Body = $email->htmlBody;
				if ($email->body) {
					$this->mailer->AltBody = $email->body;
				}
			} else {
				$this->mailer->Body = $email->body;
			}
	
			// todo: implement
			/* foreach ($email->attachments as $attachment) {
				if ($attachment->path) {
					$this->mailer->addAttachment(
						$attachment->path,
						$attachment->name,
						PHPMailer::ENCODING_BASE64,
						$attachment->mimeType
					);
				} else {
					$this->mailer->addStringAttachment(
						$attachment->content,
						$attachment->name,
						PHPMailer::ENCODING_BASE64,
						$attachment->mimeType
					);
				}
			} */
			
			$this->mailer->send();
		} catch (PHPMailerException $ex) {
			throw new SendEmailFailedException($email, $ex);
		}
		$this->clearMailer();
	}
	
	/**
	 * Clear mailer parameters
	 */
	protected function clearMailer(): void {
		$this->mailer->From = '';
		$this->mailer->FromName = '';
		$this->mailer->Sender = '';
		$this->mailer->clearAllRecipients();
		$this->mailer->clearReplyTos();
		$this->mailer->Body = '';
		$this->mailer->AltBody = '';
		$this->mailer->Subject = '';
		$this->mailer->isHTML(false);
		// $this->mailer->clearAttachments();
		// $this->mailer->clearCustomHeaders();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getSentMIMEMessage(): string {
		return $this->mailer->getSentMIMEMessage();
	}

}