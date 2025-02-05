<?php
namespace Pandora3\Logger\Filters;
use Pandora3\Contracts\LogHandlerInterface;

/**
 * Class ChannelLogFilter
 * @package Pandora3\Logger\Filters
 */
class ChannelLogFilter implements LogHandlerInterface {

	/** @var array */
	protected $channels;
	
	/** @var LogHandlerInterface */
	protected $handler;
	
	/**
	 * ChannelLogFilter constructor
	 * @param LogHandlerInterface $handler
	 * @param array $config
	 */
	public function __construct(LogHandlerInterface $handler, array $config) {
		$this->channels = $config;
		$this->handler = $handler;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getLevel(): string {
		return $this->handler->getLevel();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function handle(object $record): void {
		if (in_array($record->channel, $this->channels)) {
			$this->handler->handle($record);
		}
	}
	
}