<?php
namespace Pandora3\Http;

use Pandora3\Contracts\ResponseInterface;

/**
 * Class Response
 * @package Pandora3\Http
 */
class Response implements ResponseInterface {

	/** @var string */
	protected $content;

	/** @var int */
	protected $status;

	/** @var array */
	protected $headers;

	public const CODE_CONTINUE = 100;
	public const CODE_SWITCHING_PROTOCOLS = 101;
	public const CODE_PROCESSING = 102;
	public const CODE_EARLY_HINTS = 103;
	public const CODE_OK = 200;
	public const CODE_CREATED = 201;
	public const CODE_ACCEPTED = 202;
	public const CODE_NON_AUTHORITATIVE_INFORMATION = 203;
	public const CODE_NO_CONTENT = 204;
	public const CODE_RESET_CONTENT = 205;
	public const CODE_PARTIAL_CONTENT = 206;
	public const CODE_MULTI_STATUS = 207;
	public const CODE_ALREADY_REPORTED = 208;
	public const CODE_IM_USED = 226;
	public const CODE_MULTIPLE_CHOICES = 300;
	public const CODE_MOVED_PERMANENTLY = 301;
	public const CODE_FOUND = 302;
	public const CODE_SEE_OTHER = 303;
	public const CODE_NOT_MODIFIED = 304;
	public const CODE_USE_PROXY = 305;
	public const CODE_TEMPORARY_REDIRECT = 307;
	public const CODE_PERMANENT_REDIRECT = 308;
	public const CODE_BAD_REQUEST = 400;
	public const CODE_UNAUTHORIZED = 401;
	public const CODE_PAYMENT_REQUEST = 402;
	public const CODE_FORBIDDEN = 403;
	public const CODE_NOT_FOUND = 404;
	public const CODE_METHOD_NOT_ALLOWED = 405;
	public const CODE_NOT_ACCEPTABLE = 406;
	public const CODE_PROXY_AUTHENTICATION_REQUIRED = 407;
	public const CODE_REQUEST_TIMEOUT = 408;
	public const CODE_CONFLICT = 409;
	public const CODE_GONE = 410;
	public const CODE_LENGTH_REQUIRED = 411;
	public const CODE_PRECONDITION_FAILED = 412;
	public const CODE_PAYLOAD_TOO_LARGE = 413;
	public const CODE_URI_TOO_LONG = 414;
	public const CODE_UNSUPPORTED_MEDIA_TYPE = 415;
	public const CODE_RANGE_NOT_SATISFIABLE = 416;
	public const CODE_EXPECTATION_FAILED = 417;
	public const CODE_TEAPOT = 418;
	public const CODE_MISDIRECTED_REQUEST = 421;
	public const CODE_UNPROCESSABLE_ENTITY = 422;
	public const CODE_LOCKED = 423;
	public const CODE_FAILED_DEPENDENCY = 424;
	public const CODE_TOO_EARLY = 425;
	public const CODE_UPGRADE_REQUIRED = 426;
	public const CODE_PRECONDITION_REQUIRED = 428;
	public const CODE_TOO_MANY_REQUESTS = 429;
	public const CODE_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
	public const CODE_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
	public const CODE_INTERNAL_SERVER_ERROR = 500;
	public const CODE_NOT_IMPLEMENTED = 501;
	public const CODE_BAD_GATEWAY = 502;
	public const CODE_SERVICE_UNAVAILABLE = 503;
	public const CODE_GATEWAY_TIMEOUT = 504;
	public const CODE_HTTP_VERSION_NOT_SUPPORTED = 505;
	public const CODE_VARIANT_ALSO_NEGOTIATES = 506;
	public const CODE_INSUFFICIENT_STORAGE = 507;
	public const CODE_LOOP_DETECTED = 508;
	public const CODE_NOT_EXTENDED = 510;
	public const CODE_NETWORK_AUTHENTICATION_REQUIRED = 511;

	/**
	 * Response constructor
	 * @param string $content
	 * @param int $status
	 * @param array $headers
	 */
	public function __construct(string $content, int $status = 200, array $headers = []) {
		$this->content = $content;
		$this->status = $status;
		$this->headers = $headers;
	}

	/** @var array */
	protected static $statusText = [
		self::CODE_CONTINUE => 'Continue',
		self::CODE_SWITCHING_PROTOCOLS => 'Switching Protocols',
		self::CODE_PROCESSING => 'Processing',
		self::CODE_EARLY_HINTS => 'Early Hints',
		self::CODE_OK => 'OK',
		self::CODE_CREATED => 'Created',
		self::CODE_ACCEPTED => 'Accepted',
		self::CODE_NON_AUTHORITATIVE_INFORMATION => 'Non-Authoritative Information',
		self::CODE_NO_CONTENT => 'No Content',
		self::CODE_RESET_CONTENT => 'Reset Content',
		self::CODE_PARTIAL_CONTENT => 'Partial Content',
		self::CODE_MULTI_STATUS => 'Multi-Status',
		self::CODE_ALREADY_REPORTED => 'Already Reported',
		self::CODE_IM_USED => 'IM Used',
		self::CODE_MULTIPLE_CHOICES => 'Multiple Choices',
		self::CODE_MOVED_PERMANENTLY => 'Moved Permanently',
		self::CODE_FOUND => 'Found',
		self::CODE_SEE_OTHER => 'See Other',
		self::CODE_NOT_MODIFIED => 'Not Modified',
		self::CODE_USE_PROXY => 'Use Proxy',
		self::CODE_TEMPORARY_REDIRECT => 'Temporary Redirect',
		self::CODE_PERMANENT_REDIRECT => 'Permanent Redirect',
		self::CODE_BAD_REQUEST => 'Bad Request',
		self::CODE_UNAUTHORIZED => 'Unauthorized',
		self::CODE_PAYMENT_REQUEST => 'Payment Required',
		self::CODE_FORBIDDEN => 'Forbidden',
		self::CODE_NOT_FOUND => 'Not Found',
		self::CODE_METHOD_NOT_ALLOWED => 'Method Not Allowed',
		self::CODE_NOT_ACCEPTABLE => 'Not Acceptable',
		self::CODE_PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
		self::CODE_REQUEST_TIMEOUT => 'Request Timeout',
		self::CODE_CONFLICT => 'Conflict',
		self::CODE_GONE => 'Gone',
		self::CODE_LENGTH_REQUIRED => 'Length Required',
		self::CODE_PRECONDITION_FAILED => 'Precondition Failed',
		self::CODE_PAYLOAD_TOO_LARGE => 'Payload Too Large',
		self::CODE_URI_TOO_LONG => 'URI Too Long',
		self::CODE_UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
		self::CODE_RANGE_NOT_SATISFIABLE => 'Range Not Satisfiable',
		self::CODE_EXPECTATION_FAILED => 'Expectation Failed',
		self::CODE_TEAPOT => 'I\'m a teapot',
		self::CODE_MISDIRECTED_REQUEST => 'Misdirected Request',
		self::CODE_UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
		self::CODE_LOCKED => 'Locked',
		self::CODE_FAILED_DEPENDENCY => 'Failed Dependency',
		self::CODE_TOO_EARLY => 'Too Early',
		self::CODE_UPGRADE_REQUIRED => 'Upgrade Required',
		self::CODE_PRECONDITION_REQUIRED => 'Precondition Required',
		self::CODE_TOO_MANY_REQUESTS => 'Too Many Requests',
		self::CODE_REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request Header Fields Too Large',
		self::CODE_UNAVAILABLE_FOR_LEGAL_REASONS => 'Unavailable For Legal Reasons',
		self::CODE_INTERNAL_SERVER_ERROR => 'Internal Server Error',
		self::CODE_NOT_IMPLEMENTED => 'Not Implemented',
		self::CODE_BAD_GATEWAY => 'Bad Gateway',
		self::CODE_SERVICE_UNAVAILABLE => 'Service Unavailable',
		self::CODE_GATEWAY_TIMEOUT => 'Gateway Timeout',
		self::CODE_HTTP_VERSION_NOT_SUPPORTED => 'HTTP Version Not Supported',
		self::CODE_VARIANT_ALSO_NEGOTIATES => 'Variant Also Negotiates',
		self::CODE_INSUFFICIENT_STORAGE => 'Insufficient Storage',
		self::CODE_LOOP_DETECTED => 'Loop Detected',
		self::CODE_NOT_EXTENDED => 'Not Extended',
		self::CODE_NETWORK_AUTHENTICATION_REQUIRED => 'Network Authentication Required',
    ];

    public static function getStatusText(int $status): string {
        return self::$statusText[$status];
    }

	protected function sendHeaders(): void {
		if (headers_sent()) {
			return;
		}

		$statusText = self::getStatusText($this->status) ?? 'Unknown status';
		// $version = ($_SERVER['SERVER_PROTOCOL'] ?? '' !== 'HTTP/1.0') ? '1.1' : '1.0';
		$version = '1.1';
		header("HTTP/{$version} {$this->status} {$statusText}", true, $this->status);

		foreach ($this->headers as $header => $value) {
			header("{$header}: {$value}", false, $this->status);
		}

		/* foreach ($this->getCookies() as $cookie) {
			header('Set-Cookie: '.$cookie, false, $this->status);
		} */
	}

	public function send(): void {
		$this->sendHeaders();
		echo $this->content;
	}

}