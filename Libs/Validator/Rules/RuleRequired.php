<?php
namespace Pandora3\Validator\Rules;

use Pandora3\Contracts\UploadedFileInterface;

/**
 * Class RuleRequired
 * @package Pandora3\Validator\Rules
 */
class RuleRequired {

	/** @var string */
	public $message = 'Field "{:field}" is required'; // 'Заполните поле "{:field}"'
	
	/**
	 * RuleRequired constructor
	 * @param array $arguments
	 */
	public function __construct(array $arguments = []) { }

	/**
	 * @param mixed|null $value
	 * @param array $values
	 * @return bool
	 */
	public function validate($value, array $values = []): bool {
		if ($value instanceof UploadedFileInterface) {
			return $value->getError() === UPLOAD_ERR_OK;
		}
		return (bool) $value;
	}

}