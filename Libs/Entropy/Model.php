<?php
namespace Pandora3\Entropy;

interface ConnectionResolverInterface {
	
	/**
	 * @param string $name
	 * @return ConnectionInterface
	 */
	function getConnection(string $name): ConnectionInterface;

}

abstract class Model {

	/** @var string|array */
	protected $primaryKey = 'id';
	
	/** @var string */
	protected $connection = 'default';
	
	/** @var DatabaseManager */
	protected static $connectionResolver;
	
	/** @var static */
	protected static $instance;
	
	/**
	 * Model constructor
	 * @param array $values
	 */
	public function __construct(array $values = []) {
		;
	}
	
	public static function init(ConnectionResolverInterface $connectionResolver) {
		self::$connectionResolver = $connectionResolver;
		self::$instance = new static();
	}
	
	/**
	 * @return bool
	 */
	public function save(): bool {
		return true;
	}
	
	
	/**
	 * @return string|array
	 */
	public static function getKeyName() {
		return self::$instance->primaryKey;
	}
	
	/**
	 * @return string
	 */
	public static function getConnectionName(): string {
		return self::$instance->connection;
	}
	
	/**
	 * @param array $values
	 * @return static
	 */
	public static function create(array $values): self {
		$model = new static($values);
		$model->save();
		return $model;
	}
	
	/**
	 * @return QueryBuilder|static
	 */
	public static function query(): QueryBuilder {
		$connectionName = self::getConnectionName();
		$connection = self::$connectionResolver->getConnection($connectionName);
		return new QueryBuilder($connection, static::class);
	}
	
	/**
	 * @param array|mixed $primary
	 * @return null|static
	 */
	public static function find($primary): ?self {
		return self::query()->where(self::getKeyName(), $primary)->first();
	}

}

class Test extends Model {



}

interface ConnectionInterface {

	function select();
	
	function insert();
	
	function update();
	
	function delete();
	
	function statement();

}

class Connection implements ConnectionInterface {

	public function select() {
		;
	}
	
	public function insert() {
		;
	}
	
	public function update() {
		;
	}
	
	public function delete() {
		;
	}
	
	public function statement() {
		;
	}

}

class DatabaseManager implements ConnectionResolverInterface {
	
	/** @var ConnectionInterface[] */
	protected $connections;
	
	/**
	 * DatabaseManager constructor
	 * @param ConnectionInterface[] $connections
	 */
	public function __construct(array $connections) {
		$this->connections = $connections;
	}
	
	/**
	 * @param string $name
	 * @return ConnectionInterface
	 */
	public function getConnection(string $name): ConnectionInterface {
		if (empty($this->connections[$name])) {
			throw new \RuntimeException("Connection '{$name}' is not registered");
		}
		return $this->connections[$name];
	}
	
}

class MySqlDriver {



}

trait HasWhereQueryBuilder {

	/** @var array */
	protected $where = [];

	/**
	 * @param string $column
	 * @param string $operator
	 * @param mixed|null $value
	 * @return static
	 */
	public function where(string $column, string $operator, $value = null): self {
		/* if (is_null($value)) {
			$value = $operator;
			$operator = '=';
		} */
		
		$this->where[] = [
			'type' => QueryBuilder::WHERE_TYPE_COLUMN,
			'column' => $column,
			'operator' => $operator,
			'value' => $value,
			'boolean' => QueryBuilder::BOOLEAN_AND,
		];
		
		return $this;
	}

	/**
	 * @param string $column
	 * @param string $operator
	 * @param mixed|null $value
	 * @return static
	 */
	public function orWhere(string $column, string $operator, $value = null): self {
		$this->where[] = [
			'type' => QueryBuilder::WHERE_TYPE_COLUMN,
			'column' => $column,
			'operator' => $operator,
			'value' => $value,
			'boolean' => QueryBuilder::BOOLEAN_OR,
		];
	
		return $this;
	}
	
	/**
	 * @param string $column
	 * @param array $values
	 * @return static
	 */
	public function whereIn(string $column, array $values): self {
		$this->where[] = [
			'type' => QueryBuilder::WHERE_TYPE_IN,
			'column' => $column,
			'values' => $values,
			'boolean' => QueryBuilder::BOOLEAN_AND,
		];
		
		return $this;
	}
	
	/**
	 * @param string $column
	 * @param array $values
	 * @return static
	 */
	public function orWhereIn(string $column, array $values): self {
		$this->where[] = [
			'type' => QueryBuilder::WHERE_TYPE_IN,
			'column' => $column,
			'values' => $values,
			'boolean' => QueryBuilder::BOOLEAN_OR,
		];
		
		return $this;
	}
	
	/**
	 * @param string $column
	 * @param array $values
	 * @return static
	 */
	public function whereNotIn(string $column, array $values): self {
		$this->where[] = [
			'type' => QueryBuilder::WHERE_TYPE_NOT_IN,
			'column' => $column,
			'values' => $values,
			'boolean' => QueryBuilder::BOOLEAN_AND,
		];
		
		return $this;
	}
	
	/**
	 * @param string $column
	 * @param array $values
	 * @return static
	 */
	public function orWhereNotIn(string $column, array $values): self {
		$this->where[] = [
			'type' => QueryBuilder::WHERE_TYPE_NOT_IN,
			'column' => $column,
			'values' => $values,
			'boolean' => QueryBuilder::BOOLEAN_OR,
		];
		
		return $this;
	}

	protected function addBindings(array $bindings): void {
		if ($bindings) {
			$this->bindings = array_merge($this->bindings, $bindings);
		}
	}
	
	/**
	 * @param string $sql
	 * @param array $bindings
	 * @return static
	 */
	public function whereRaw(string $sql, array $bindings = []): self {
		$this->where[] = [
			'type' => QueryBuilder::WHERE_TYPE_RAW,
			'sql' => $sql,
			'boolean' => QueryBuilder::BOOLEAN_AND,
		];
		$this->addBindings($bindings);
		
		return $this;
	}
	
	/**
	 * @param string $sql
	 * @param array $bindings
	 * @return static
	 */
	public function orWhereRaw(string $sql, array $bindings = []): self {
		$this->where[] = [
			'type' => QueryBuilder::WHERE_TYPE_RAW,
			'sql' => $sql,
			'boolean' => QueryBuilder::BOOLEAN_OR,
		];
		$this->addBindings($bindings);
		
		return $this;
	}
	
	/**
	 * @param \Closure $func
	 * @return static
	 */
	public function whereSub(\Closure $func): self {
		$whereBuilder = new WhereQueryBuilder();
		$func($whereBuilder);
		$this->where[] = [
			'type' => QueryBuilder::WHERE_TYPE_SUB,
			'where' => $whereBuilder->getWHere(),
			'boolean' => QueryBuilder::BOOLEAN_AND,
		];
		$this->addBindings($whereBuilder->getBindings());
		
		return $this;
	}
	
	/**
	 * @param \Closure $func
	 * @return static
	 */
	public function orWhereSub(\Closure $func): self {
		$whereBuilder = new WhereQueryBuilder();
		$func($whereBuilder);
		$this->where[] = [
			'type' => QueryBuilder::WHERE_TYPE_SUB,
			'where' => $whereBuilder->getWHere(),
			'boolean' => QueryBuilder::BOOLEAN_OR,
		];
		$this->addBindings($whereBuilder->getBindings());
		
		return $this;
	}

}

class WhereQueryBuilder {

	use HasWhereQueryBuilder;

	protected $bindings = [];
	
	public function getBindings(): array {
		return $this->bindings;
	}
	
	public function getWHere(): array {
		return $this->where;
	}

}

class QueryBuilder {

	use HasWhereQueryBuilder;
	
	public const WHERE_TYPE_COLUMN = 'column';
	public const WHERE_TYPE_IN = 'in';
	public const WHERE_TYPE_NOT_IN = 'notIn';
	public const WHERE_TYPE_RAW = 'raw';
	public const WHERE_TYPE_SUB = 'sub';
	
	protected $operators = [
		'=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like', // 'like binary', 'ilike', 'not ilike',
        '&', '|', 'rlike', 'not rlike', 'regexp', 'not regexp',
	];
	
	/* public const OPERATOR_EQUAL = '=';
	public const OPERATOR_NOT_EQUAL = '!=';
	public const OPERATOR_LESS = '<';
	public const OPERATOR_GREATER = '>';
	public const OPERATOR_LESS_OR_EQUAL = '<='; */
	
	public const BOOLEAN_AND = 'and';
	public const BOOLEAN_OR = 'or';
	
	/** @var ConnectionInterface */
	protected $connection;
	
	/** @var string */
	protected $modelClassName;
	
	protected $bindings = [];

	/**
	 * QueryBuilder constructor
	 * @param ConnectionInterface $connection
	 * @param string $modelClassName
	 */
	public function __construct(ConnectionInterface $connection, string $modelClassName) {
		$this->connection = $connection;
		$this->modelClassName = $modelClassName;
	}
	
	
	/**
	 * @param int $value
	 * @return static
	 */
	public function skip(int $value): self {
		;
		
		return $this;
	}
	
	/**
	 * @param int $value
	 * @return static
	 */
	public function take(int $value): self {
		;
		
		return $this;
	}
	
	
	
	/**
	 * @return null|self|Model
	 */
	public function first(): ?Model {
		$modelClassName = $this->modelClassName;
		return new $modelClassName([]);
	}
	
	/**
	 * @return array
	 */
	public function get(): array {
		return [];
	}
	
	/**
	 * @return array
	 */
	public function compile(): array {
		$sql = '';
		$params = [];
		return [$sql, $params];
	}
	
	/* public function toSql(): string {
		[$sql, $params] = $this->compile();
		return $sql;
	} */
	
}