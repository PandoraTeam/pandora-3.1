<?php
namespace Pandora3\Router;

/**
 * Class Route
 * @package Pandora3\Router
 */
class Route {

	/** @var string */
	public $path;
	
	/** @var string */
	public $pattern;
	
	/** @var int */
	public $methodFlags;

	/** @var mixed */
	public $handler;

	/** @var string */
	public $name;
	
	/** @var array */
	public $middlewares = [];
	
	public const METHOD_FLAG_NONE       = 0;
	
	public const METHOD_FLAG_GET        = 1;
	public const METHOD_FLAG_POST       = 2;
	public const METHOD_FLAG_PUT        = 4;
	public const METHOD_FLAG_DELETE     = 8;
	public const METHOD_FLAG_PATCH      = 16;
	public const METHOD_FLAG_HEAD       = 32;
	public const METHOD_FLAG_OPTIONS    = 64;
	
	public const METHOD_FLAG_ANY = (
		Route::METHOD_FLAG_GET | Route::METHOD_FLAG_POST |
		Route::METHOD_FLAG_PUT | Route::METHOD_FLAG_DELETE |
		Route::METHOD_FLAG_PATCH | Route::METHOD_FLAG_HEAD |
		Route::METHOD_FLAG_OPTIONS
	);
	
	/**
	 * Route constructor
	 * @param string $path
	 * @param string $pattern
	 * @param int $methodFlags
	 * @param mixed $handler
	 * @param string $name
	 */
	public function __construct(string $path, string $pattern, int $methodFlags, $handler, string $name = '') {
		$this->path = $path;
		$this->pattern = $pattern;
		$this->methodFlags = $methodFlags;
		$this->handler = $handler;
		$this->name = $name;
	}
	
	/**
	 * @param string $name
	 * @return Route
	 */
	public function name(string $name): self {
		$this->name = $name;
		return $this;
	}
	
	/**
	 * @param $middleware
	 * @return Route
	 */
	public function middleware($middleware): self {
		$this->middlewares[] = $middleware;
		return $this;
	}
	
	/**
	 * @param array ...$middlewares
	 * @return $this
	 */
	public function middlewares(...$middlewares): self {
		$this->middlewares = array_merge($this->middlewares, $middlewares);
		return $this;
	}

}