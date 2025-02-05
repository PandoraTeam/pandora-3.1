<?php
namespace Pandora3\Eloquent;

use Illuminate\Database\Capsule\Manager as EloquentManager;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;

/**
 * Class EloquentConnection
 * @package Pandora3\Eloquent
 */
class EloquentConnection {

	/** @var array */
	protected $params;

	/** @var EloquentManager */
	protected $manager;
	
	/** @var DatabaseManager|null */
	protected $database;
	
	/** @var Dispatcher|null */
	protected $dispatcher;

	/**
	 * Eloquent constructor
	 * @param array $params
	 * @param EloquentManager $manager
	 */
	public function __construct(array $params, EloquentManager $manager) {
		$this->params = array_replace([
			'charset' => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix' => '',
		], $params);
		
		$this->manager = $manager;
		$this->manager->addConnection($this->params, $params['connectionName'] ?? 'default');

		$this->dispatcher = new Dispatcher();
		$this->manager->setEventDispatcher($this->dispatcher);
	}
	
	/**
	 * @return array
	 */
	public function __debugInfo() {
		$result = get_object_vars($this);
		$result['params']['password'] = '***'; // protect password value when dumping
		return $result;
	}
	
	/**
	 * @param bool $global
	 * @return EloquentManager
	 */
	public static function createManager(bool $global = false): EloquentManager {
		$manager = new EloquentManager();
		if ($global) {
			$manager->setAsGlobal();
		}
		return $manager;
	}
	
	/* *
	 * @return Dispatcher
	 */
	/* protected function getDispatcher(): Dispatcher {
		if (is_null($this->dispatcher)) {
			if (is_null($this->database)) {
				throw new \RuntimeException('Database connection is not connected');
			}
			$this->dispatcher = new Dispatcher();
			$this->database->setEventDispatcher($this->dispatcher);
		}
		return $this->dispatcher;
	} */
	
	/**
	 * @param \Closure $callback
	 * @param string|array $events
	 */
	public function registerEvent(\Closure $callback, $events = QueryExecuted::class): void {
		$this->dispatcher->listen($events, $callback);
	}
	
	/**
	 * @return SchemaBuilder
	 */
	public function getSchemaBuilder(): SchemaBuilder {
		return $this->database->getSchemaBuilder();
	}
	
	/**
	 * @return EloquentManager
	 */
	public function getManager(): EloquentManager {
		return $this->manager;
	}
	
	/**
	 * @return DatabaseManager
	 */
	public function getDatabase(): DatabaseManager {
		if (is_null($this->database)) {
			throw new \RuntimeException('Database connection is not connected');
		}
		return $this->database;
	}
	
	/**
	 * Create database connection
	 */
	public function connect(): void {
		$this->manager->bootEloquent();
		$this->database = $this->manager->getDatabaseManager();
	}
	
	/**
	 * Close database connection
	 */
	public function close(): void {
		$this->database->disconnect();
	}

}