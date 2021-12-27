<?php
namespace Pandora3\Validator;

use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\ValidatorInterface;
use Pandora3\Validator\Exceptions\ValidationException;
use Pandora3\Validator\Rules\RuleFunction;

/**
 * Class Validator
 * @package Pandora3\Validator
 */
class Validator implements ValidatorInterface {

	/** @var array */
	protected $rules;

	/** @var array */
	protected $messages = [];
	
	/** @var array */
	protected $ruleMessages = [];

	/** @var array */
	protected static $ruleTypes = [];
	
	/**
	 * Validator constructor
	 * @param array $rules
	 * @param array $messages
	 */
	public function __construct(array $rules = [], array $messages = []) {
		$this->rules = $rules;
		$this->ruleMessages = $messages;
	}

	/**
	 * @param string $type
	 * @param string $className
 	 */
	public static function registerRule(string $type, string $className): void {
		self::$ruleTypes[$type] = $className;
	}

	/**
	 * @param array $ruleTypes
	 */
	public static function registerRules(array $ruleTypes): void {
		self::$ruleTypes = array_replace(self::$ruleTypes, $ruleTypes);
	}

	/**
	 * @param ContainerInterface $container
	 */
	public static function use(ContainerInterface $container): void {
		$container->bind(ValidatorInterface::class, Validator::class);
	}

	/**
	 * @return array
	 */
	protected function getRules(): array {
		return $this->rules;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMessages(): array {
		return $this->messages;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFieldMessage(string $fieldName): string {
		return $this->messages[$fieldName] ?? '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate($data): void {
		if ($data instanceof RequestInterface) {
			$data = $data->all();
		}
		$isValid = true;
		$this->messages = [];
		foreach ($this->getRules() as $fieldName => $rules) {
			if (is_string($rules)) {
				$rules = [$rules];
			}
			$value = $data[$fieldName] ?? null;
			foreach ($rules as $key => $ruleType) {
				$arguments = [];
				if (!is_numeric($key)) {
					$arguments = $ruleType;
					$ruleType = $key;
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
				if ($ruleType instanceof \Closure) {
					$rule = new RuleFunction($ruleType);
				} else {
					$rule = $this->createRule($ruleType, $arguments);
				}
				if (!$rule->validate($value, $data)) {
					$message = $this->ruleMessages[$fieldName][$ruleType] ?? $rule->message; // todo: support to customize different messages of a single rule
					$message = str_replace('{:field}', $fieldName, $message);
					// str_replace('{:field}', $label ?: $fieldName, $message);
					$this->messages[$fieldName] = $message;
					$isValid = false;
					break;
				}
			}
		}
		if (!$isValid) {
			throw new ValidationException($this->messages);
		}
	}

	/**
	 * @param string $ruleType
	 * @param array $arguments
	 * @return mixed
	 */
	protected function createRule(string $ruleType, array $arguments) {
		$ruleClass = self::$ruleTypes[$ruleType] ?? null;
		if (is_null($ruleClass)) {
			throw new \RuntimeException("Unregistered rule type '$ruleType'");
		}
		if (!class_exists($ruleClass)) {
			throw new \RuntimeException("Rule class '$ruleClass' not found");
		}
		return new $ruleClass($arguments);
	}

}

Validator::registerRules([
	'required' => Rules\RuleRequired::class,
	'unique' => Rules\RuleUnique::class,
	'equal' => Rules\RuleEqual::class,
	'email' => Rules\RuleEmail::class,
	'file' => Rules\RuleFile::class,
	'date' => Rules\RuleDate::class,
]);