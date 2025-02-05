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
			 // todo: use typed exception ContainerException
			throw new \RuntimeException("Unable to bind null dependency for [$abstract]");
		}
		$this->bindDependency($abstract, $dependency);
	}

	/**
	 * {@inheritdoc}
	 */
	public function singleton(string $abstract, $dependency = null): void {
		if (!is_null($dependency)) {
			$this->bindDependency($abstract, $dependency);
		}
		$this->isSingleton[$abstract] = true;
	}
	
	/**
	 * @param string $abstract
	 * @param $dependency
	 */
	protected function bindDependency(string $abstract, $dependency): void {
		$previous = $this->dependencies[$abstract] ?? null;
		if ($dependency !== $previous) {
			if (isset($this->instances[$abstract])) {
				// todo: use typed exception ContainerException
				throw new \RuntimeException("Unable to modify dependency for [$abstract] due to it's instance already created");
			}
			unset($this->canMake[$abstract]);
		}

		$this->dependencies[$abstract] = $dependency;
	}

	/**
	 * @param \ReflectionParameter $parameter
	 * @param string $className
	 * @return mixed
	 */
	protected function makePrimitive(\ReflectionParameter $parameter, string $className) {
		if (!$parameter->isDefaultValueAvailable()) {
			$parameterName = $parameter->getName();
			// todo: use typed exception ContainerException
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
				// todo: use typed exception ContainerException
				throw new \RuntimeException("No dependency bound to [$className]");
			}
			// todo: use typed exception ContainerException
			throw new \RuntimeException("Class [$className] not found");
		}

		$reflection = new \ReflectionClass($className);
		if (!$reflection->isInstantiable()) {
			// todo: use typed exception ContainerException
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
	 * @param array $array
	 * @return bool
	 */
	protected function arrayIsList(array $array): bool {
		foreach ($array as $key => $value) {
			if (is_int($key)) {
				return true;
			}
			break;
		}
		return false;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function make(string $abstract, array $params = []): object {
		if (isset($this->instances[$abstract])) {
			return $this->instances[$abstract];
		}
		
		if (array_key_exists($abstract, self::$queuedMake)) {
			// todo: use typed exception ContainerException
			throw new \LogicException("Recursion while instantiating [$abstract] dependency. Try to use \$container->build() instead of make() inside of closure");
		}

		$dependency = $this->dependencies[$abstract] ?? $abstract;
		$isSingleton = $this->isSingleton[$abstract] ?? false;

		if ($dependency instanceof \Closure) {
			self::$queuedMake[$abstract] = true;
			if ($this->arrayIsList($params)) {
				$instance = $dependency($this, ...$params);
			} else {
				$instance = $dependency($this, $params);
			}
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
		$className = static::class;
		trigger_error("Undefined property '$property' for [$className]", E_USER_WARNING);
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