<?php
namespace Pandora3\Application\ExceptionHandler;

/**
 * Class ErrorInfo
 * @package Pandora3\Application
 */
class ErrorInfo {
	
	/**
	 * ErrorInfo constructor
	 * @param int $code
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 */
	public function __construct(int $code, string $message, string $file = '', int $line = 0) {
		$this->code = $code;
		$this->message = $message;
		$this->file = $file;
		$this->line = $line;
	}
	
	/** @var string */
	public $message;

	/** @var int */
	public $code;

	/** @var string */
	public $file;
	
	/** @var int */
	public $line;
	
}