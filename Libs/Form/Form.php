<?php
namespace Pandora3\Form;

use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\SanitizerInterface;
use Pandora3\Contracts\SessionInterface;
use Pandora3\Contracts\ValidationExceptionInterface;
use Pandora3\Form\Exceptions\ValidationFormException;
use Pandora3\Form\Fields\FormField;

/**
 * Class Form
 * @package Pandora3\Form
 */
abstract class Form {

	/** @var ContainerInterface */
	protected $container;

	/** @var RequestInterface */
	protected $request;
	
	/** @var \Closure */
	protected $getSecret;

	/** @var string */
	protected $method = 'post';

	/** @var bool */
	protected $filesUpload = false;

	/** @var array */
	protected $htmlAttribs = [];

	/** @var array */
	protected $values = [];

	/** @var array */
	protected $fieldParams;

	/** @var array */
	protected static $fieldTypes = [];
	
	/**
	 * Form constructor
	 * @param ContainerInterface $container
	 * @param RequestInterface $request
	 * @param \Closure $getSecret
	 * @param object|array $values
	 */
	public function __construct(ContainerInterface $container, RequestInterface $request, \Closure $getSecret, $values = []) {
		$this->container = $container;
		$this->request = $request;
		$this->getSecret = $getSecret;

		if (!is_array($values) && !($values instanceof \stdClass)) {
			$className = static::class;
			throw new \LogicException("Form [$className] values must be an array or stdClass instance");
		}
		$this->setValues((array) $values);
		$this->fieldParams = $this->fields();

		$defaultValues = [];
		foreach ($this->fieldParams as $fieldName => $fieldParams) {
			$type = $fieldParams['type'] ?? null;
			if (is_null($type)) {
				$className = static::class;
				throw new \LogicException("Field '$fieldName' type not set in [$className]");
			}
			if ($this->fieldTypeIsFile($type)) {
				$this->filesUpload = true;
			}
			$default = $fieldParams['default'] ?? null;
			if (!is_null($default) && !array_key_exists($fieldName, $this->values)) {
				$defaultValues[$fieldName] = $default;
			}
		}

		$this->setValues($defaultValues);
		// $this->setValues(array_replace($defaultValues, (array) $values));
		$this->load();
	}

	/**
	 * @return array
	 */
	abstract protected function fields(): array;

	/**
	 * Before load event
	 */
	protected function beforeLoad(): void { }

	/**
	 * After load event
	 * @param array $values
	 * @return array
	 */
	protected function afterLoad(array $values): array {
		return $values;
	}

	/**
	 * @return array
	 */
	protected function filters(): array {
		return [];
	}
	
	/**
	 * @param string $fieldType
	 * @return bool
	 */
	protected function fieldTypeIsFile(string $fieldType): bool {
		return $fieldType === 'file';
	}

	/**
	 * @return SanitizerInterface
	 */
	protected function createSanitizer(): SanitizerInterface {
		return $this->container->make(SanitizerInterface::class, ['filters' => $this->filters()]);  // todo: probably use factory
	}

	/**
	 * @param array $values
	 * @return array
	 */
	protected function sanitize(array $values): array {
		$sanitizer = $this->createSanitizer();
		return $sanitizer->sanitize($values);
	}
	
	/**
	 * @param string $fieldName
	 * @param mixed|null $default
	 * @return mixed|null
	 */
	public function get(string $fieldName, $default = null) {
		if (!$fieldName) {
			return null;
		}
		$methodName = 'get'.ucfirst($fieldName).'Value';
		if (method_exists($this, $methodName)) {
			return $this->$methodName();
		}
		return $this->values[$fieldName] ?? $default;
	}

	/**
	 * @param string $fieldName
	 * @param mixed|null $value
	 */
	public function set(string $fieldName, $value): void {
		$methodName = 'set'.ucfirst($fieldName).'Value';
		if (method_exists($this, $methodName)) {
			$this->$methodName($value);
			return;
		}
		$this->values[$fieldName] = $value;
	}

	/**
	 * @param string $fieldName
	 */
	public function clear(string $fieldName): void {
		unset($this->values[$fieldName]);
	}

	/**
	 * @param string $fieldName
	 * @return mixed|null
	 */
	public function __get($fieldName) {
		return $this->get($fieldName);
	}

	/**
	 * @param string $fieldName
	 * @param mixed|null $value
	 */
	public function __set($fieldName, $value) {
		$this->set($fieldName, $value);
	}

	/**
	 * @param string $fieldName
	 * @return bool
	 */
	public function __isset($fieldName) {
		return !is_null($this->get($fieldName));
	}

	/**
	 * @param string $fieldName
	 */
	public function __unset($fieldName) {
		$this->clear($fieldName);
	}

	/**
	 * @return array
	 */
	public function getValues(): array {
		return $this->values;
	}

	/**
	 * @param array|object|null $values
	 */
	public function setValues($values): void {
		if (!$values) {
			return;
		}
		foreach ($values as $fieldName => $value) {
			$this->set($fieldName, $value);
		}
	}

	/**
	 * @param array ...$arguments
	 * @return array
	 */
	public function only(...$arguments): array {
		return array_intersect_key($this->values, array_flip($arguments));
	}

	/**
	 * @param array ...$arguments
	 * @return array
	 */
	public function except(...$arguments): array {
		return array_diff_key($this->values, array_flip($arguments));
	}

	/**
	 * @param string $type
	 * @param string $className
 	 */
	public static function registerField(string $type, string $className): void {
		self::$fieldTypes[$type] = $className;
	}

	/**
	 * @param array $fieldTypes
	 */
	public static function registerFields(array $fieldTypes): void {
		self::$fieldTypes = array_replace(self::$fieldTypes, $fieldTypes);
	}

	/**
	 * @return string
	 */
	public function getSubmitUri(): string {
		return $this->request->getUri();
	}

	/**
	 * @return array
	 */
	protected function getHtmlAttribs(): array {
		return array_replace(
			$this->htmlAttribs,
			[
				'method' => $this->method,
				'action' => $this->getSubmitUri(),
			],
			$this->filesUpload ?
				['enctype' => 'multipart/form-data'] : []
		);
	}

	/**
	 * Loads values from request
	 * @param null|string $method
	 */
	public function load(?string $method = null): void {
		$this->beforeLoad();
		
		if (is_null($method)) {
			$method = $this->method;
		}
		$request = $this->request;

		/* if ($method === 'post' && !$request->isPost()) {
			return;
		} */

		$hasValues = false;
		$values = [];
		foreach (array_keys($this->fieldParams) as $fieldName) {
			if ($method === 'post') {
				$value = null;
				if ($this->filesUpload) {
					$value = $request->file($fieldName);
				}
				$value = $value ?? $request->post($fieldName);
			} else {
				$value = $request->get($fieldName);
			}
			if (!is_null($value)) {
				$hasValues = true;
			}
			$value = $value ?? $this->values[$fieldName] ?? null;
			if (!is_null($value)) {
				$values[$fieldName] = $value;
			}
		}

		if (!$hasValues) {
			if (
				$method === 'get' ||
				($method === 'post' && !$request->isPost())
			) {
				return;
			}
		}

		$values = $this->sanitize($values);

		$values = $this->afterLoad($values);
		foreach ($values as $fieldName => $value) {
			$this->set($fieldName, $value);
		}
	}

	/**
	 * @return string
	 */
	public function getToken() {
		/** @var SessionInterface $session */
		$session = $this->container->make(SessionInterface::class);
		$server = $this->request->server();
		$userAgent = $server['HTTP_USER_AGENT'] ?? '';
		$data = implode(':', [
			$userAgent,
			$session->getId(),
			$this->getSubmitUri()
		]);
		$getSecret = $this->getSecret;
		$secret = $getSecret();
		return hash_hmac('sha512', $data.$secret, $secret);
	}
	
	/**
	 * Validate SCRF token
	 */
	public function validateToken(): void {
		if ($this->method !== 'post') {
			throw new \LogicException("Form should have method 'post' to support token validation");
		}
		if (!$this->request->isPost()) {
			throw new \LogicException("Request method is not post");
		}
		if ($this->request->post('_token') !== $this->getToken()) {
			throw new ValidationFormException("Invalid CSRF token", static::class);
		}
	}
	
	/**
	 * @return bool
	 */
	public function doValidateToken(): bool {
		try {
			$this->validateToken();
			return true;
		} catch (ValidationExceptionInterface $ex) {
			return false;
		}
	}

	/**
	 * @param array $attribs
	 * @return string
	 */
	public function begin(array $attribs = []): string {
		$attribs = array_replace($this->getHtmlAttribs(), $attribs);

		$htmlAttribs = '';
		foreach ($attribs as $key => $value) {
			if (!is_null($value)) {
				$htmlAttribs .= $key.'="'.htmlspecialchars($value).'" ';
			}
		}
		
		$output = '<form '.$htmlAttribs.'>';
		if ($this->method === 'post') {
			$output .= '<input type="hidden" name="_token" value="'.$this->getToken().'">';
		}
		return $output;
	}
	
	/**
	 * @return string
	 */
	public function end(): string {
		return '</form>';
	}

	/**
	 * @param string $fieldName
	 * @return array|null
	 */
	public function getFieldParams(string $fieldName): ?array {
		return $this->fieldParams[$fieldName] ?? null;
	}

	/**
	 * @param string $fieldName
	 * @param array $params
	 * @return FormField
	 */
	protected function createField(string $fieldName, array $params): FormField {
		$fieldParams = $this->fieldParams[$fieldName] ?? null;
		$className = static::class;
		if (is_null($fieldParams)) {
			throw new \LogicException("Field '$fieldName' doesn't exist in [$className]");
		}
		$type = $fieldParams['type'];
		$fieldClass = self::$fieldTypes[$type] ?? null;
		if (is_null($fieldClass)) {
			throw new \LogicException("Unregistered field type '$type' for field '$fieldName' in [$className]");
		}
		if (!class_exists($fieldClass)) {
			throw new \LogicException("Field class '$fieldClass' not found for field '$fieldName'");
		}
		$params = array_replace($fieldParams, $params);
		if (array_key_exists('value', $params)) {
			$value = $params['value'];
			unset($params['value']);
			if ($value instanceof \Closure) {
				$value = $value($this->get($fieldName));
			}
		} else {
			$value = $this->get($fieldName);
		}
		/* $value = array_key_exists('value', $params)
			? $params['value']
			: $this->get($fieldName); */
		return new $fieldClass($fieldName, $value, $params);
	}

	/**
	 * @param string $fieldName
	 * @param array $params
	 * @return string
	 */
	public function field(string $fieldName, array $params = []): string {
		$field = $this->createField($fieldName, $params);
		return $field->render();
	}

}

Form::registerFields([
	'hidden' => Fields\FieldHidden\FieldHidden::class,
	'input' => Fields\FieldText\FieldText::class,
	'password' => Fields\FieldText\FieldPassword::class,
	'email' => Fields\FieldText\FieldEmail::class,
	'textarea' => Fields\FieldTextarea\FieldTextarea::class,
	'file' => Fields\FieldFile\FieldFile::class,
	'date' => Fields\FieldDate\FieldDate::class,
	'calendar' => Fields\FieldDate\FieldCalendar::class,
	'select' => Fields\FieldSelect\FieldSelect::class,
	'radio' => Fields\FieldRadio\FieldRadio::class,
	'checkbox' => Fields\FieldCheckbox\FieldCheckbox::class,
	'switch' => Fields\FieldSwitch\FieldSwitch::class,
	'checkboxGroup' => Fields\FieldCheckboxGroup\FieldCheckboxGroup::class,
]);