<?php
namespace Pandora3\Form;

use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\RequestInterface;
use Pandora3\Contracts\ValidationExceptionInterface;
use Pandora3\Contracts\ValidatorInterface;
use Pandora3\Form\Exceptions\ValidationFormException;

/**
 * Class ValidationForm
 * @package Pandora3\Form
 */
abstract class ValidationForm extends Form {

	/** @var ValidatorInterface */
	protected $validator;

	/** @var array */
	protected $messages;

	/**
	 * @param ContainerInterface $container
	 * @param RequestInterface $request
	 * @param array $values
	 */
	public function __construct(ContainerInterface $container, RequestInterface $request, $values = []) {
		parent::__construct($container, $request, $values);
		$this->messages = $request->getAttribute('validationMessages') ?? []; // todo: probably use flashMessages
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
	 * Validate field values
	 */
	public function validate(): void {
		if ($this->method === 'post' && !$this->request->isPost()) {
			throw new ValidationFormException("Request method is not post");
		}
		$this->validator = $this->createValidator();
		$this->validator->validate($this->getValues());
		$this->messages = $this->validator->getMessages();
		$this->afterValidate();
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
		if (!$this->validator) {
			return '';
		}
		return $this->validator->getFieldMessage($fieldName);
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