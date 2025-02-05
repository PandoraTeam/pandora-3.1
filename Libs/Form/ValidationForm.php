<?php
namespace Pandora3\Form;

use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\ValidationExceptionInterface;
use Pandora3\Contracts\ValidatorInterface;
use Pandora3\Form\Exceptions\ValidationFormException;
use Pandora3\Form\Fields\FormField;

/**
 * Class ValidationForm
 * @package Pandora3\Form
 */
abstract class ValidationForm extends Form {

	/** @var ValidatorInterface */
	protected $validator;

	/** @var array */
	protected $messages = [];

	/**
	 * @param ContainerInterface $container
	 * @param RequestInterface $request
	 * @param \Closure $getSecret
	 * @param array $values
	 */
	public function __construct(ContainerInterface $container, RequestInterface $request, \Closure $getSecret, $values = []) {
		parent::__construct($container, $request, $getSecret, $values);
		$messages = $request->getAttribute('validationMessages') ?? [];
		if ($messages) {
			$formName = $request->getAttribute('validationForm') ?? null;
			// when formName is null load validation messages for any form
			if (is_null($formName) || $formName === static::class) {
				$this->messages = $messages; // todo: probably use flashMessages
			}
		}
	}

	/**
	 * Provides validation rules
	 * @return array
	 */
	abstract protected function rules(): array;
	
	/**
	 * @return array
	 */
	protected function ruleMessages(): array {
		return [];
	}
	
	/**
	 * After validate event
	 */
	protected function afterValidate(): void { }

	/**
	 * @return ValidatorInterface
	 */
	protected function createValidator(): ValidatorInterface {
		return $this->container->make(ValidatorInterface::class, [ // todo: probably use factory
			'rules' => $this->rules(),
			'messages' => $this->ruleMessages(),
		]);
	}
	
	/**
	 * @return array
	 */
	protected function getFieldLabels(): array {
		$labels = [];
		foreach ($this->fieldParams as $fieldName => $fieldParams) {
			$label = $fieldParams['label'] ?? null;
			if ($label) {
				$labels[$fieldName] = $label;
			}
		}
		return $labels;
	}

	/**
	 * Validate field values
	 */
	public function validate(): void {
		if ($this->method === 'post' && !$this->request->isPost()) {
			throw new \LogicException("Request method is not post");
		}
		$this->validateToken();
		$this->validator = $this->createValidator();
		$this->validator->setFieldLabels($this->getFieldLabels());
		$isValid = true;
		try {
			$this->validator->validate($this->getValues());
		} catch (ValidationExceptionInterface $ex) {
			$isValid = false;
		}
		$this->messages = $this->validator->getMessages();
		$this->afterValidate();
		if (!$isValid) {
			throw new ValidationFormException($this->messages, static::class);
		}
	}

	/**
	 * Validate field values without exception
	 * @return bool
	 */
	public function doValidate(): bool {
		try {
			$this->validate();
			return true;
		} catch (ValidationExceptionInterface $ex) {
			return false;
		}
	}

	/**
	 * @return array
	 */
	public function getMessages(): array {
		return $this->messages;
	}

	/**
	 * @param string $fieldName
	 * @return string
	 */
	public function getFieldMessage(string $fieldName): string {
		return $this->messages[$fieldName] ?? '';
	}
	
	/**
	 * @param string $fieldName
	 * @return bool
	 */
	public function isFieldValid(string $fieldName): bool {
		return !isset($this->messages[$fieldName]);
	}
	
	/**
	 * @param string $fieldName
	 * @param array $params
	 * @return FormField
	 */
	protected function createField(string $fieldName, array $params): FormField {
		$params['isValid'] = $this->isFieldValid($fieldName);
		return parent::createField($fieldName, $params);
	}
	
	/**
	 * @return string
	 */
	public function messages(): string {
		if (!$this->messages) {
			return '';
		}
		ob_start();
			echo '<div class="form-messages">';
				foreach ($this->messages as $message) {
					echo '<div class="message message-danger">';
						echo '<span>'.$message.'</span>';
					echo '</div>';
					/* if (!is_array($fieldMessages)) {
						$fieldMessages = [$fieldMessages];
					}
					foreach ($fieldMessages as $message) {
						echo '<div class="message message-danger">';
							echo '<span>'.$message.'</span>';
						echo '</div>';
					} */
				}
			echo '</div>';
		return ob_get_clean();
	}

}