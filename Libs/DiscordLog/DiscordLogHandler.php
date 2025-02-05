<?php
namespace Pandora3\DiscordLog;

use Pandora3\Contracts\LogHandlerInterface;
use Pandora3\Logger\Logger;

class DiscordLogHandler implements LogHandlerInterface {

	/** @var string */
	protected $level;
	
	/** @var string */
	protected $userName;
	
	/** @var string */
	protected $webhook;
	
	/** @var array|null */
	protected $colors;
	
	/** @var array|null */
	protected $icons;
	
	protected const DefaultUserName = 'App';
	
	protected static $defaultColors = [
		Logger::Emergency	=> 'FF1F58',
		Logger::Alert		=> 'FF1F58',
		Logger::Critical	=> 'FF1F58',
		Logger::Error		=> 'FF1F58',
		Logger::Warning		=> 'FF8A21',
		Logger::Notice		=> '9b9b9b',
		Logger::Info		=> '397AFF',
		Logger::Debug		=> '9b36ff',
	];
	
	protected static $defaultIcons = [
		Logger::Emergency	=> ':fire:', // FF1F58
		Logger::Alert		=> ':fire:',
		Logger::Critical	=> ':rotating_light:',
		Logger::Error		=> ':rotating_light:',
		Logger::Warning		=> ':warning:',
		Logger::Notice		=> ':information_source:',
		Logger::Info		=> ':information_source:',
		Logger::Debug		=> ':gear:',
	];
	
	/**
	 * @param string $level
	 * @param string $webhook
	 * @param array $options
	 */
	public function __construct(string $level, string $webhook, array $options = []) {
		$this->level = $level;
		$this->webhook = $webhook;
		if (!$this->webhook) {
			throw new \InvalidArgumentException("Argument 'webhook' is required");
		}
		$this->userName = !empty($options['username']) ? $options['username'] : self::DefaultUserName;
		$this->colors = $options['colors'] ?? null;
		$this->icons = $options['icons'] ?? null;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getLevel(): string {
		return $this->level;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function handle(object $record): void {
		$color = $this->colors[$record->level] ?? self::$defaultColors[$record->level];
		$icon = $this->icons[$record->level] ?? self::$defaultIcons[$record->level];
		
		$title = ($icon ? $icon.'  ' : '').ucfirst($record->level) . ($record->channel ? '.'.$record->channel : '');
		$content = '@everyone';
		$time = new \DateTime('now');

		/** @var \Throwable|null $exception */
		$exception = $record->context['exception'] ?? null;

		$description = $record->message;

		if (!is_null($exception)) {
			$file = str_replace('/var/www/uc7f1da0a/data/www/contracts.u-238.ru', '', $exception->getFile()); // todo: make dynamic, not hardcocded path
			$description .= " in **{$file}:{$exception->getLine()}**\n";
			$prev = $exception;
			while ($prev = $prev->getPrevious()) {
				$file = str_replace('/var/www/uc7f1da0a/data/www/contracts.u-238.ru', '', $prev->getFile());
				$description .= "  {$prev->getMessage()} in **{$file}:{$prev->getLine()}**\n";
			}
		} else {
			$description .= "\n";
		}

		$context = $record->context;
		if ($context) {
			unset($context['exception']);
			$contextJson = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
			if ($contextJson !== '[]') {
				$description .= "\n**Context**\n" .
					"```js\n{$contextJson}\n```";
			}
		}
		
		if (!is_null($exception)) {
			$trace = str_replace('/var/www/uc7f1da0a/data/www/contracts.u-238.ru', '', $exception->getTraceAsString());
			$description .= "\n**Stack trace**\n" .
				"```\n{$trace}\n```";
		}
		
		$data = [
			'content' => $content,
			'username' => $this->userName,
			'embeds' => [
				array_filter([
					'title' => $title,
					'description' => $description,
					'timestamp' => $time->format('c'),
					'color' => $color ? hexdec($color) : null,
				])
			]
		];
		
		$this->sendNotification($data);
	}
	
	/**
	 * @param array $data
	 */
	protected function sendNotification(array $data): void {
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => $this->webhook,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => ['Content-type: application/json'],
			// CURLOPT_FOLLOWLOCATION => true,
			// CURLOPT_HEADER => false,
			// CURLOPT_RETURNTRANSFER => true,
		]);
		curl_exec($curl); // $response = curl_exec($curl);
		curl_close($curl);
	}
	
}