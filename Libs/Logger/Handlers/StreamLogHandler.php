<?php
namespace Pandora3\Logger\Handlers;

use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\LogHandlerInterface;
use Pandora3\Contracts\StreamInterface;
use Pandora3\File\FileStream;

/**
 * Class FileLogHandler
 * @package Pandora3\Logger\Handlers
 */
class StreamLogHandler implements LogHandlerInterface {
	
	/** @var string */
	protected $level;
	
	/** @var StreamInterface */
	protected $stream;

	/**
	 * @param string $level
	 * @param StreamInterface $stream
	 */
	public function __construct(string $level, StreamInterface $stream) {
		$this->level = $level;
		$this->stream = $stream;
	}
	
	/**
	 * @param ContainerInterface $container
	 */
	public static function use(ContainerInterface $container): void {
		$container->bind(StreamLogHandler::class, function(ContainerInterface $c, $config) {
			$stream = new FileStream($config['path']);
			return new StreamLogHandler($config['level'], $stream);
		});
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getLevel(): string {
		return $this->level;
	}
	
	/**
	 * @param object $record
	 * @return string
	 */
	protected function formatMessage(object $record): string {
		$level = ucfirst($record->level) . ($record->channel ? '.'.$record->channel : '');
		$time = date('Y-m-d H:i:sP');
		$message = "{$time} [$level]: {$record->message}";
		$context = $record->context;
		/** @var \Throwable|null $exception */
		$exception = $context['exception'] ?? null;
		$error = $context['error'] ?? null;
		unset($context['exception']);
		unset($context['error']);
		if ($exception) {
			$file = str_replace('/var/www/uc7f1da0a/data/www/contracts.u-238.ru', '', $exception->getFile()); // todo: make dynamic, not hardcocded path
			$message .= " in {$file}:{$exception->getLine()}\n";
		} else if ($error) {
			$file = str_replace('/var/www/uc7f1da0a/data/www/contracts.u-238.ru', '', $error['file']);
			$line = $error['line'];
			$message .= " in {$file}:{$line}\n";
		} else {
			$message .= "\n";
		}
		if ($context) {
			$contextJson = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			$message .= "  Context: {$contextJson}\n";
		}
		if ($exception) {
			$prev = $exception;
			while ($prev = $prev->getPrevious()) {
				$file = str_replace('/var/www/uc7f1da0a/data/www/contracts.u-238.ru', '', $prev->getFile());
				$message .= "  {$prev->getMessage()} in {$file}:{$prev->getLine()}\n";
			}
			$message .= "  Stack trace:\n";
			$trace = str_replace('/var/www/uc7f1da0a/data/www/contracts.u-238.ru', '', $exception->getTraceAsString());
			$message .= "  ".str_replace("\n", "\n  ", $trace)."\n";
		}
		return $message;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function handle(object $record): void {
		/* if (true) { // temporary
			return;
		} */
		$formattedMessage = $this->formatMessage($record);
		$this->stream->write($formattedMessage);
	}
	
}