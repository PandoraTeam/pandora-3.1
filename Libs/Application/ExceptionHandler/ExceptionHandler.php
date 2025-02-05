<?php
namespace Pandora3\Application\ExceptionHandler;

use Pandora3\Authentication\Authentication;
use Pandora3\Contracts\LoggerInterface;

/**
 * Class ExceptionHandler
 * @package Pandora3\Application
 */
class ExceptionHandler {

	public const Emergency	= 'emergency';
	public const Alert		= 'alert';
	public const Critical	= 'critical';
	public const Error		= 'error';
	public const Warning	= 'warning';
	public const Notice		= 'notice';
	public const Info		= 'info';
	public const Debug		= 'debug';
	
	protected static $errorCodesTitle = [
		E_COMPILE_ERROR => 'E_COMPILE_ERROR',
		E_PARSE => 'E_PARSE',
		E_ERROR => 'E_ERROR',
		E_USER_ERROR => 'E_USER_ERROR',
		E_CORE_ERROR => 'E_CORE_ERROR',
		E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
		E_WARNING => 'E_WARNING',
		E_USER_WARNING => 'E_USER_WARNING',
		E_CORE_WARNING => 'E_CORE_WARNING',
		E_COMPILE_WARNING => 'E_COMPILE_WARNING',
		E_NOTICE => 'E_NOTICE',
		E_USER_NOTICE => 'E_USER_NOTICE',
		E_DEPRECATED => 'E_DEPRECATED',
		E_USER_DEPRECATED => 'E_USER_DEPRECATED',
		E_STRICT => 'E_STRICT',
	];

	/** @var LoggerInterface */
	protected $logger;
	
	/** @var Authentication */
	protected $authentication;
	
	/**
	 * ExceptionHandler constructor
	 * @param LoggerInterface $logger
	 * @param Authentication $authentication
	 */
	public function __construct(LoggerInterface $logger, Authentication $authentication) {
		$this->logger = $logger;
		$this->authentication = $authentication;
		set_error_handler(\Closure::fromCallable([$this, 'handleError']), E_ALL);
		$this->logger->setContext($this->getContext());
	}
	
	/**
	 * @param \Throwable $exception
	 */
	public function reportException(\Throwable $exception): void {
		$this->logger->critical($exception->getMessage(), ['exception' => $exception]);
	}
	
	/**
	 * @param ErrorInfo $error
	 */
	protected function reportError(ErrorInfo $error): void {
		$message = $error->message;
		$code = $error->code;
		$file = $error->file;
		$line = $error->line;

		$level = self::Notice;
		$channel = null;
		switch ($code) {
			case E_COMPILE_ERROR:
				$level = self::Alert;
				$channel = 'Compile';
				break;
			case E_PARSE:
				$level = self::Alert;
				$channel = 'Parse';
				break;

			case E_ERROR:
			case E_USER_ERROR:
				$level = self::Critical;
				break;
			case E_CORE_ERROR:
				$level = self::Critical;
				$channel = 'Core';
				break;

			case E_RECOVERABLE_ERROR:
				$level = self::Error;
				break;

			case E_WARNING:
			case E_USER_WARNING:
				$level = self::Warning;
				break;
			case E_CORE_WARNING:
				$level = self::Warning;
				$channel = 'Core';
				break;
			case E_COMPILE_WARNING:
				$level = self::Warning;
				$channel = 'Compile';
				break;

			case E_NOTICE:
			case E_USER_NOTICE:
				$level = self::Notice;
				break;

			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				$level = self::Notice;
				$channel = 'Deprecated';
				break;

			case E_STRICT:
				$level = self::Notice;
				$channel = 'Strict';
				break;
		}

		if ($file) {
			$file = str_replace('/var/www/uc7f1da0a/data/www/contracts.u-238.ru', '', $file); // todo: temporary
		}

		$context = [
			'error' => [
				'code' => self::$errorCodesTitle[$error->code] ?? 'UNKNOWN_ERROR:'.$code,
				'file' => $file,
				'line' => $line
			],
		];
		if ($file) {
			$message .= " in {$file}:{$line}";
		}
		$this->logger->log($level, $message, $context, $channel);
	}
	
	/**
	 * @param int $code
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 * @return bool
	 */
	protected function handleError(int $code, string $message, string $file = '', int $line = 0): bool {
		$this->reportError(new ErrorInfo($code, $message, $file, $line));
		return true;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getContext(): array {
		return [
			'signature' => [
				'uri' => $_SERVER['REQUEST_URI'] ?? null,
				'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
				'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
				'userId' => $this->authentication->getUserId(), // $_SESSION['authenticationUserId'] ?? null,
			]
		];
	}
	
}