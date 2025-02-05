<?php
namespace Pandora3\Events;

use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\DispatcherInterface;

/**
 * Class Dispatcher
 * @package Pandora3\Libs\Events
 */
class Dispatcher implements DispatcherInterface {
	
	/**
	 * @param ContainerInterface $container
	 */
	public static function use(ContainerInterface $container): void {
		$container->bind(DispatcherInterface::class, Dispatcher::class);
		$container->singleton(Dispatcher::class);
	}
	
	/** @var array */
	protected $listeners = [];
	
	/**
	 * @param string $eventClass
	 * @param \Closure $listener
	 */
	public function listen(string $eventClass, \Closure $listener): void {
		$this->listeners[$eventClass][] = $listener;
	}
	
	/**
	 * @param object $event
	 * @return object
	 */
	public function dispatch(object $event): object {
		$eventClass = get_class($event);
		$listeners = $this->listeners[$eventClass] ?? [];
		foreach ($listeners as $listener) {
			$listener($event);
		}
		return $event;
	}

}