<?php
namespace Pandora3\Http;

use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\UploadedFileFactoryInterface;
use Pandora3\Contracts\UploadedFileInterface;
use Pandora3\Contracts\UriInterface;

/**
 * Class Request
 * @package Pandora3\Application
 */
class Request implements RequestInterface {

	/** @var string */
	public $method;

	/** @var UriInterface */
	public $uri;

	/** @var array */
	protected $get;

	/** @var array */
	protected $post;
	
	/** @var array */
	protected $cookies;

	/** @var array */
	protected $files;

	/** @var array */
	protected $server;
	
	/** @var array */
	protected $attributes;

	/* * @var array */
	// protected $headers;

	public const METHOD_GET = 'get';
	public const METHOD_HEAD = 'head';
	public const METHOD_POST = 'post';
	public const METHOD_PUT = 'put';
	public const METHOD_DELETE = 'delete';
	public const METHOD_PATCH = 'patch';
	
	/**
	 * Request constructor
	 * @param string $method
	 * @param UriInterface|string $uri
	 * @param array $get
	 * @param array $post
	 * @param array $cookies
	 * @param UploadedFileInterface[] $files
	 * @param array $server
	 * @param array $attributes
	 */
	public function __construct(
		string $method,
		$uri,
		// $content, // string|StreamInterface
		array $get = [],
		array $post = [],
		array $cookies = [],
		array $files = [],
		array $server = [],
		array $attributes = []
		// ?array $headers = null
	) {
		$this->method = strtolower($method);
		if (is_string($uri)) {
			$uri = new Uri($uri); // $uri, $get
		}
		$this->uri = $uri;
		$this->get = $get;
		$this->post = $post;
		$this->cookies = $cookies;
		$this->files = $files;
		$this->server = $server;
		$this->attributes = $attributes;
		// $this->headers = $headers;
	}
	
	/* protected static function getHttpHeaders(array $server): array {
		$headers = [];
		foreach ($server as $key => $value) {
			$key = strtolower($key);
			if (substr($key, 0, 5) === 'http_') {
				$key = substr($key, 5);
			} else if (!in_array($key, ['content_length', 'content_md5', 'content_type'])) {
				continue;
			}
			$key = str_replace('_', '-', $key);
			$key = preg_replace_callback('#\w+(?:-|$)#', static function($matches) {
				return ucfirst($matches[0]);
			}, $key);
			$headers[$key] = $value;
		}
		return $headers;
	}

	public function getHeaders(): array {
		if (is_null($this->headers)) {
			$this->headers = self::getHttpHeaders($this->server);
		}
		return $this->headers;
	} */

	/**
	 * Create Request instance from globals
	 * @param UploadedFileFactoryInterface|null $fileFactory
	 * @return static
	 */
	public static function create(UploadedFileFactoryInterface $fileFactory = null) {
		// $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$uri = preg_replace('/(\?.*)?(#[\w\-]+)?$/', '', $_SERVER['REQUEST_URI'], 1);
		if ($uri === false || is_null($uri)) {
			\Logger::warning("Unable to parse REQUEST_URI", ['requestUri' => $_SERVER['REQUEST_URI']]); // todo: temp
		}
		return new static(
			$_SERVER['REQUEST_METHOD'] ?: self::METHOD_GET,
			$uri, // new Uri(), // $_SERVER['REQUEST_URI'] ?? '/', $_SERVER['QUERY_STRING'] ?? ''
			$_GET, $_POST, $_COOKIE, self::normalizeFiles($_FILES, $fileFactory), $_SERVER
		);
	}

	/**
	 * @param array $files
	 * @param UploadedFileFactoryInterface|null $fileFactory
	 * @return array
	 */
	protected static function normalizeFiles(array $files, UploadedFileFactoryInterface $fileFactory = null): array {
		$files = self::collapseFiles($files);
		return self::createUploadedFiles($files, $fileFactory);
	}
	
	/**
	 * @param array $data
	 * @param UploadedFileFactoryInterface|null $fileFactory
	 * @return array
	 */
	protected static function createUploadedFiles(array $data, UploadedFileFactoryInterface $fileFactory = null): array {
		$res = [];
		foreach ($data as $key => $file) {
			if (
				(isset($file['name']) && !is_array($file['name'])) ||
				(isset($file['tmp_name']) && !is_array($file['tmp_name'])) ||
				(isset($file['error']) && !is_array($file['error']))
			) {
				$res[$key] = $fileFactory->createUploadedFile($file);
				continue;
			}
			$res[$key] = self::createUploadedFiles($file, $fileFactory);
		}
		return $res;
	}

	/**
	 * @param array $files
	 * @return array
	 */
	protected static function collapseFiles(array $files): array {
		$res = [];
		foreach ($files as $key => $file) {
			$res[$key] = [];
			foreach ($file as $fileKey => $value) {
				self::collapseFilesRecursive($res[$key], $value, $fileKey);
			}
		}
		return $res;
	}
	
	/**
	 * @param array $res
	 * @param mixed $data
	 * @param string $fileKey
	 */
	protected static function collapseFilesRecursive(array &$res, $data, string $fileKey): void {
		if (!is_array($data)) {
			$res[$fileKey] = $data;
			return;
		}
		foreach ($data as $key => $value) {
			if (!isset($res[$key])) {
				$res[$key] = [];
			}
			self::collapseFilesRecursive($res[$key], $value, $fileKey);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMethod(): string {
		return $this->method;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isPost(): bool {
		return $this->method === self::METHOD_POST;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function isAjax(): bool {
		$headerRequestedWith = $this->server['HTTP_X_REQUESTED_WITH'] ?? '';
		return strtolower($headerRequestedWith) === 'xmlhttprequest';
	}
	
	/**
	 * @param string $method
	 * @return bool
	 */
	public function isMethod(string $method): bool {
		return $this->method === $method;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUri(): string {
		return $this->uri->getUri();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getRefererUri(): ?string {
		return $this->server['HTTP_REFERER'] ?? null;
	}
	
	/* public function getUri(): string {
		if (is_null($this->uri)) {
			$queryString = $this->getQueryString();
			$this->uri = $this->path.($queryString ? '?'.$queryString : '');
		}
		return $this->uri;
	}

	public function getQueryString(): string {
		return $server['QUERY_STRING'] ?? '';
	} */

	/**
	 * {@inheritdoc}
	 */
	public function get(string $key) {
		return $this->get[$key] ?? null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function post(string $key) {
		return $this->post[$key] ?? null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function file(string $key) {
		return $this->files[$key] ?? null;
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function hasFile(string $key): bool {
		return !is_null($this->file($key));
	}

	/**
	 * {@inheritdoc}
	 */
	public function cookie(string $key) {
		return $this->cookies[$key] ?? null;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getAttribute(string $key) {
		return $this->attributes[$key] ?? null;
	}

	/**
	 * @param string $key
	 * @return mixed|null
	 */
	public function input(string $key) {
		return (
			$this->files[$key] ??
			$this->post[$key] ??
			$this->get[$key] ?? null
		);
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function has(string $key): bool {
		return !is_null($this->input($key));
	}

	/**
	 * @param string[] ...$keys
	 * @return array
	 */
	public function only(...$keys): array {
		$vars = $this->all();
		return array_intersect_key($vars, array_flip($keys));
	}

	/**
	 * @param string[] ...$keys
	 * @return array
	 */
	public function except(...$keys): array {
		$vars = $this->all();
		return array_diff_key($vars, array_flip($keys));
	}

	/**
	 * {@inheritdoc}
	 */
	public function all(?string $method = null): array {
		switch ($method) {
			case self::METHOD_GET:
				return $this->get;
			case self::METHOD_POST:
				return array_replace($this->post, $this->files);
			default:
				return array_replace($this->get, $this->post, $this->files);
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function files(): array {
		return $this->files;
	}

	/**
	 * {@inheritdoc}
	 */
	public function cookies(): array {
		return $this->cookies;
	}

	/**
	 * {@inheritdoc}
	 */
	public function server(): array {
		return $this->server;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValues(): array {
		return $this->get;
	}

	/**
	 * {@inheritdoc}
	 */
	public function postValues(): array {
		return $this->post;
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributes(): array {
		return $this->attributes;
	}

	/* *
	 * @param string $key
	 * @return bool
	 */
	/* public function filled(string $key): bool {
		;
	} */

	/* *
	 * @param string $pattern
	 * @return bool
	 */
	/* public function isPath(string $pattern): bool {
		;
	} */

}