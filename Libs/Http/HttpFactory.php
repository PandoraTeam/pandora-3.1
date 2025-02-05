<?php
namespace Pandora3\Http;

use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\RequestFactoryInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\ResponseFactoryInterface;
use Pandora3\Contracts\ResponseInterface;
use Pandora3\Contracts\UploadedFileFactoryInterface;

/**
 * Class HttpFactory
 * @package Pandora3\Application
 */
class HttpFactory implements ResponseFactoryInterface, RequestFactoryInterface {

	/** @var UploadedFileFactoryInterface */
	protected $fileFactory;
	
	/**
	 * HttpFactory constructor
	 * @param UploadedFileFactoryInterface $fileFactory
	 */
	public function __construct(UploadedFileFactoryInterface $fileFactory) {
		$this->fileFactory = $fileFactory;
	}
	
	/**
	 * @param ContainerInterface $container
	 */
	public static function use(ContainerInterface $container): void {
		$container->bind(RequestInterface::class, Request::class);
		$container->bind(RequestFactoryInterface::class, HttpFactory::class);
		$container->bind(ResponseFactoryInterface::class, HttpFactory::class);
		$container->singleton(HttpFactory::class);
	}
	
	/**
	 * {@inheritdoc}
	 */
	function createRequest(): RequestInterface {
		return Request::create($this->fileFactory);
	}

	/**
	 * {@inheritdoc}
	 */
	public function createResponse(string $content, int $code, array $headers = []): ResponseInterface {
		return new Response($content, $code, $headers);
	}

}