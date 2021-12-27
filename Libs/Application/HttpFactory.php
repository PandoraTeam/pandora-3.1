<?php
namespace Pandora3\Application;

use Pandora3\Contracts\RequestFactoryInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\ResponseFactoryInterface;
use Pandora3\Contracts\ResponseInterface;
use Pandora3\Contracts\UploadedFileFactoryInterface;
use Pandora3\Http\Request;
use Pandora3\Http\Response;

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