<?php
namespace Pandora3\Container;

use Pandora3\Contracts\ContainerInterface;

/**
 * Class Container
 * @package Pandora3\Container
 */
class Container implements ContainerInterface {

	/** @var array */
	protected $dependencies = [];

	/** @var array */
	protected $isSingleton = [];

	/** @var array */
	protected $instances = [];

	/** @var array */
	protected $canMake = [];

	/** @var array */
	protected static $queuedMake = [];

	/**
	 * {@inheritdoc}
	 */
	public function bind(string $abstract, $dependency): void {
		if (is_null($dependency)) {
			 // todo: ContainerException
			throw new \RuntimeException("Unable to bind null dependency for [$abstract]");
		}

		$previous = $this->dependencies[$abstract] ?? null;
		if ($dependency !== $previous) {
			if (isset($this->instances[$abstract])) {
				throw new \RuntimeException("Unable to modify dependency for [$abstract] due to it's instance already created");
			}
			unset($this->canMake[$abstract]);
		}

		$this->dependencies[$abstract] = $dependency;
	}

	/**
	 * {@inheritdoc}
	 */
	public function singleton(string $abstract, $dependency = null): void {
		if (!is_null($dependency)) {
			$previous = $this->dependencies[$abstract] ?? null;
			if ($dependency !== $previous) {
				if (isset($this->instances[$abstract])) {
					throw new \RuntimeException("Unable to modify dependency for [$abstract] due to it's instance already created");
				}
				unset($this->canMake[$abstract]);
			}

			$this->dependencies[$abstract] = $dependency;
		}

		$this->isSingleton[$abstract] = true;
	}

	/**
	 * @param \ReflectionParameter $parameter
	 * @param string $className
	 * @return mixed
	 */
	protected function makePrimitive(\ReflectionParameter $parameter, string $className) {
		if (!$parameter->isDefaultValueAvailable()) {
			$parameterName = $parameter->getName();
			throw new \RuntimeException("Parameter '$parameterName' not resolved for class [$className]");
		}
		return $parameter->getDefaultValue();
	}

	/**
	 * @param string $className
	 * @param \ReflectionParameter[] $dependencies
	 * @param array $params
	 * @return array
	 */
	protected function resolveDependencies(string $className, array $dependencies, array $params = []): array {
		$result = [];
		foreach ($dependencies as $dependency) {
			if (array_key_exists($dependency->name, $params)) {
				$result[] = $params[$dependency->name];
				continue;
			}
			$class = $dependency->getClass();
			$result[] = !is_null($class)
				? $this->make($class->name)
				: $this->makePrimitive($dependency, $className);
		}
		return $result;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function build(string $className, array $params = []): object {
		if (!class_exists($className)) {
			if (interface_exists($className)) {
				throw new \RuntimeException("No dependency bound to [$className]");
			}
			throw new \RuntimeException("Class [$className] not found");
		}

		$reflection = new \ReflectionClass($className);
		if (!$reflection->isInstantiable()) {
			throw new \RuntimeException("Dependency [$className] is not instantiable");
		}

		$constructor = $reflection->getConstructor();
		if (is_null($constructor)) {
			return new $className();
		}

		$dependencies = $constructor->getParameters();
		$arguments = $this->resolveDependencies($className, $dependencies, $params);

		return $reflection->newInstanceArgs($arguments);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function make(string $abstract, array $params = []): object {
		if (isset($this->instances[$abstract])) {
			return $this->instances[$abstract];
		}
		
		if (array_key_exists($abstract, self::$queuedMake)) {
			throw new \LogicException("Recursion while instantiating [$abstract] dependency. Try to use \$container->build() instead of make() inside of closure"); // todo: typed exception
		}

		$dependency = $this->dependencies[$abstract] ?? $abstract;
		$isSingleton = $this->isSingleton[$abstract] ?? false;

		if ($dependency instanceof \Closure) {
			self::$queuedMake[$abstract] = true;
			$instance = $dependency($this, ...$params);
			unset(self::$queuedMake[$abstract]);
		} else if ($abstract === $dependency) {
			$instance = $this->build($dependency, $params);
		} else if (is_object($dependency)) {
			$instance = $dependency;
			$isSingleton = true;
		} else {
			$instance = $this->make($dependency, $params);
			if (isset($this->instances[$abstract])) {
				$isSingleton = true;
			}
		}

		if ($isSingleton) {
			$this->instances[$abstract] = $instance;
		}

		return $instance;
	}

	/**
	 * @param string $abstract
	 * @return bool
	 */
	public function checkCanMake($abstract): bool {
		if (isset($this->instances[$abstract])) {
			return true;
		}

		$dependency = $this->dependencies[$abstract] ?? $abstract;

		if (
			$dependency instanceof \Closure ||
			is_object($dependency)
		) {
			return true;
		}

		if ($abstract === $dependency) {
			return class_exists($abstract);
		}

		return $this->checkCanMake($dependency);
	}

	/**
	 * @param string $abstract
	 * @return bool
	 */
	public function canMake($abstract): bool {
		if (!isset($this->canMake[$abstract])) {
			$this->canMake[$abstract] = $this->checkCanMake($abstract);
		}
		return $this->canMake[$abstract];
	}

	/**
	 * @param string $key
	 * @param array ...$params
	 * @return object
	 */
	public function get(string $key, ...$params): object {
		return $this->make($key, $params);
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function has(string $key): bool {
		return $this->canMake($key);
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function isset(string $key): bool {
		return !is_null($this->get($key));
	}

	/**
	 * @param string $property
	 * @return object|null
	 */
	public function __get(string $property): ?object {
		if (array_key_exists($property, $this->dependencies)) {
			return $this->make($property);
		}
		// $className = static::class; // todo: warning
		// logException(new \Exception("Undefined property '$property' for [$className]", E_NOTICE));
		return null;
	}

	/**
	 * @param string $property
	 * @return bool
	 */
	public function __isset(string $property): bool {
		return $this->isset($property);
	}

}