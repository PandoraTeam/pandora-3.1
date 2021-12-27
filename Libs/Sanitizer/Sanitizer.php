<?php
namespace Pandora3\Sanitizer;

use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\SanitizerInterface;
use Pandora3\Sanitizer\Filters\FilterFunction;

/**
 * Class Validator
 * @package Pandora3\Validator
 */
class Sanitizer implements SanitizerInterface {

	/** @var array */
	protected $filters;

	/** @var array */
	protected static $filterTypes = [];
	
	/**
	 * Validator constructor
	 * @param array $filters
	 */
	public function __construct(array $filters = []) {
		$this->filters = $filters;
	}

	/**
	 * @param string $type
	 * @param string $className
 	 */
	public static function registerFilter(string $type, string $className): void {
		self::$filterTypes[$type] = $className;
	}

	/**
	 * @param array $filterTypes
	 */
	public static function registerFilters(array $filterTypes): void {
		self::$filterTypes = array_replace(self::$filterTypes, $filterTypes);
	}

	/**
	 * @param ContainerInterface $container
	 */
	public static function use(ContainerInterface $container): void {
		$container->bind(SanitizerInterface::class, Sanitizer::class);
	}

	/**
	 * @return array
	 */
	protected function getFilters(): array {
		return $this->filters;
	}

	/**
	 * {@inheritdoc}
	 */
	public function sanitize($data): array {
		if ($data instanceof RequestInterface) {
			$data = $data->all();
		}
		foreach ($this->getFilters() as $fieldName => $filters) {
			if (is_string($filters)) {
				$filters = [$filters];
			}
			$value = $data[$fieldName] ?? null;
			foreach ($filters as $key => $filterType) {
				$arguments = [];
				if (!is_numeric($key)) {
					$arguments = $filterType;
					$filterType = $key;
					if (is_bool($arguments)) {
						$arguments = ['param' => '', 'enabled' => $arguments];
					} else if (!is_array($arguments)) {
						$arguments = ['param' => $arguments];
					}
				}
				$isEnabled = $arguments['enabled'] ?? true;
				if (!$isEnabled) {
					continue;
				}
				if ($filterType instanceof \Closure) {
					$filter = new FilterFunction($filterType);
				} else {
					$filter = $this->createFilter($filterType, $arguments);
				}
				$value = $filter->apply($value);
			}
			$data[$fieldName] = $value;
		}
		return $data;
	}

	/**
	 * @param string $filterType
	 * @param array $arguments
	 * @return mixed
	 */
	protected function createFilter(string $filterType, array $arguments) {
		$filterClass = self::$filterTypes[$filterType] ?? null;
		if (is_null($filterClass)) {
			throw new \RuntimeException("Unregistered filter type '$filterType'");
		}
		if (!class_exists($filterClass)) {
			throw new \RuntimeException("Filter class '$filterClass' not found");
		}
		return new $filterClass($arguments);
	}

}

Sanitizer::registerFilters([
	'trim' => Filters\FilterTrim::class,
	'escape' => Filters\FilterEscape::class,
	'lower' => Filters\FilterLower::class,
	'upper' => Filters\FilterUpper::class,
	'capitalize' => Filters\FilterCapitalize::class,
	'cast' => Filters\FilterCast::class,
	'decimal' => Filters\FilterDecimal::class,
	'date' => Filters\FilterDate::class,
	// 'dateTime' => Filters\FilterDateTime::class,
]);