<?php
namespace Pandora3\Logger\Filters;
use Pandora3\Contracts\LogHandlerInterface;

/**
 * Class LevelLogFilter
 * @package Pandora3\Logger\Filters
 */
class LevelLogFilter implements LogHandlerInterface {

	/** @var array */
	protected $levels;
	
	/** @var LogHandlerInterface */
	protected $handler;
	
	/**
	 * LevelLogFilter constructor
	 * @param LogHandlerInterface $handler
	 * @param array $config
	 */
	public function __construct(LogHandlerInterface $handler, array $config) {
		$this->levels = $config;
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
		if (in_array($record->level, $this->levels)) {
			$this->handler->handle($record);
		}
	}
	
}