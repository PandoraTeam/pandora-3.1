<?php
namespace Pandora3\Security\Middlewares;

use Pandora3\Contracts\LoggerInterface;
use Pandora3\Contracts\MiddlewareInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\ResponseInterface;
use Pandora3\Security\Security;

/**
 * Class RequestAnalyzerMiddleware
 * @package Pandora3\Security\Middlewares
 */
class RequestAnalyzerMiddleware implements MiddlewareInterface {

	/** @var LoggerInterface */
	protected $logger;
	
	/**
	 * @param LoggerInterface $logger
	 */
	public function __construct(LoggerInterface $logger) {
		$this->logger = $logger;
	}
	
	/**
	 * @param RequestInterface $request
	 */
	protected function analyzeRequest(RequestInterface $request): void {
		// file_get_contents('php://input');
		if ($request->isPost()) {
			$params = array_replace($request->getValues(), $request->postValues());
		} else {
			$params = $request->getValues();
		}
		
		// $request->getUri() '^/.env'
		
		$total = 0;
		$i = 0;
		foreach ($params as $value) {
			if (is_array($value) || $value === '') {
				continue;
			}
			$c = $value[0];
			$advancedCheck = false;
			$suspicious = 0;
			if (
				$c === ';' || $c === "'" || $c === '"' || $c === '`' ||
				$c === ')' || $c === '(' || $c === '<' || $c === '>' ||
				$c === '#' || preg_match('#^(www|http|web-inf|url)#i', $value)
			) {
				$advancedCheck = true;
			}
			if (preg_match_all('#system\.ini|\#exec|%3Cscript|<script|\bonerror\bonfocus|php://|convert\.base64-encode|data:text/|%3Cimg|<img|information_schema|var/www|var/log|src/pandora3|java\.lang|utl_inaddr|get_host_name|pg_sleep|randomblob#i', $value, $matches)) {
				$suspicious += count($matches[0]) * 10;
				$advancedCheck = true;
			}
			if (!$advancedCheck && preg_match('#\bpasswd|\bunion|\bfrom|\bselect|\bwaitfor|\bdelay|\bcase\s+when|javascript:|;base64|;url|\.php|google\.com#i', $value)) {
				$advancedCheck = true;
			}
			if ($advancedCheck) {
				if (strlen($value) >= 128) {
					$value = substr($value, 0, 128);
				}
				$value = strtolower($value);
				if (preg_match_all('#\.\./|\.\.\\\\|\\\\\.\.|/\.\.|c:/|c:\\\\|<!--|-->|--!>|%3E|%3C|/db\.php|/App\.php|\bwhen\s+not\s+null|\bwaitfor\s+delay|%0D|%0A|%0d|%0a|><|/\*|\bas\s+varchar|0\s+else\s+1|\bunion\s+select|\bselect\s+null|\bsleep\(|\bwhere\s+0|\band\s+exists|\band\s+0|\band\s+1|\bor\s+1|1\s*=\s*1|1\s*=\s*2|\balert\(|\bprompt\(|\bpasswd|\bwaitfor|\bcase\s+when|javascript:|;url|google\.com:80|google\.com/search#', $value, $matches)) {
					$suspicious += count($matches[0]);
				}
				if ($suspicious >= 2) {
					$total += $suspicious;
				}
			}
			$i++;
			if ($i >= 20) {
				break;
			}
		}
		if ($total === 0) {
			return;
		}
		
		$message = '';
		if ($request->isPost()) {
			$i = 0;
			foreach ($request->postValues() as $key => $value) {
				if (is_array($value) || $value === '') {
					continue;
				}
				$message .= "{$key}: {$value}\n";
				$i++;
				if ($i >= 30) {
					$message .= "...cropped other parameters\n";
					break;
				}
			}
		}
		$this->logger->info(
			"Request is suspicious, rating {$total}\n".$message,
			[], Security::LogChannel
		);
	}
	
	/**
	 * {@inheritdoc}
	 */
	function process(RequestInterface $request, \Closure $next, array $arguments): ResponseInterface {
		$this->analyzeRequest($request);
		return $next($request, ...$arguments);
	}

}