<?php
namespace Pandora3\Mailer;

/**
 * Class Email
 * @package Pandora3\Mailer
 */
class Email {

	/** @var array */
	public $to = [];

	/** @var array */
	public $cc = [];

	/** @var array */
	public $bcc = [];

	/** @var array */
	public $replyTo = [];

	/** @var Attachment[] */
	public $attachments = [];

	/** @var array */
	public $from;

	/** @var string */
	public $subject;

	/** @var string */
	public $body;

	/** @var string */
	public $htmlBody;

	/**
	 * @param string $email
	 * @param string|null $name
	 * @return static
	 */
	public function from(string $email, ?string $name = null): self {
		$this->from = ['email' => $email, 'name' => $name];
		return $this;
	}

	/**
	 * @param string $subject
	 * @return static
	 */
	public function subject(string $subject): self {
		$this->subject = $subject;
		return $this;
	}

	/**
	 * @param string $body
	 * @return static
	 */
	public function body(string $body): self {
		$this->body = $body;
		return $this;
	}

	/**
	 * @param string $htmlBody
	 * @return static
	 */
	public function htmlBody(string $htmlBody): self {
		$this->htmlBody = $htmlBody;
		return $this;
	}

	/**
	 * @param string $email
	 * @param string|null $name
	 * @return static
	 */
	public function addTo(string $email, ?string $name = null): self {
		$this->to[] = ['email' => $email, 'name' => $name];
		return $this;
	}

	/**
	 * @param string $email
	 * @param string|null $name
	 * @return static
	 */
	public function addCC(string $email, ?string $name = null): self {
		$this->cc[] = ['email' => $email, 'name' => $name];
		return $this;
	}

	/**
	 * @param string $email
	 * @param string|null $name
	 * @return static
	 */
	public function addBCC(string $email, ?string $name = null): self {
		$this->bcc[] = ['email' => $email, 'name' => $name];
		return $this;
	}

	/**
	 * @param string $email
	 * @param string|null $name
	 * @return static
	 */
	public function addReplyTo(string $email, ?string $name = null): self {
		$this->replyTo[] = ['email' => $email, 'name' => $name];
		return $this;
	}

	// todo: temporary not implemented
	/* *
	 * @param Attachment $attachment
	 * @return static
	 */
	/* public function addAttachment(Attachment $attachment): self {
		$this->attachments[] = $attachment;
		return $this;
	} */

}