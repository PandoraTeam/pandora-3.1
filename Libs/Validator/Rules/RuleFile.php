<?php
namespace Pandora3\Validator\Rules;

use Pandora3\Contracts\UploadedFileInterface;

/**
 * Class RuleFile
 * @package Pandora3\Validator\Rules
 */
class RuleFile {

	/** @var string */
	public $message = '';
	
	/** @var array|null */
	protected $allowedExtensions;
	
	/**
	 * RuleFile constructor
	 * @param array $arguments
	 */
	public function __construct(array $arguments = []) {
		$extensions = $arguments['extension'] ?: null;
		if ($extensions && !is_array($extensions)) {
			$extensions = explode(',', $extensions);
		}
		$this->allowedExtensions = $extensions;
	}
	
	/**
	 * @param int $error
	 * @return string
	 */
	protected function getErrorMessage(int $error): string {
		$messages = [
			UPLOAD_ERR_INI_SIZE => 'File size exceeds limit', // 'Превышен максимальный размер файла ..MB'
			UPLOAD_ERR_FORM_SIZE => 'File size exceeds form limit', // 'Превышен максимальный размер файла для данной формы ..MB'
			UPLOAD_ERR_PARTIAL => 'File was only partially uploaded', // 'Файл был получен только частично'
			UPLOAD_ERR_NO_FILE => 'No file was uploaded', // 'Файл не был загружен'
			UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder', // 'Отсутствует временная папка'
			UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk', // 'Не удалось записать файл на диск'
			UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload', // 'Модуль PHP остановил загрузку файла'
		];
		return $messages[$error] ?? "Unknown error ($error)";
	}

	/**
	 * @param mixed|null $value
	 * @param array $values
	 * @return bool
	 */
	public function validate($value, array $values = []): bool {
		if (!$value) {
			return true;
		}
		if (!($value instanceof UploadedFileInterface)) {
			$this->message = 'Field "{:field}" should be a file'; // 'Поле "{:field}" должно быть файлом'
			return false;
		}
		$error = $value->getError();
		if ($error && $error !== UPLOAD_ERR_NO_FILE) {
			$errorMessage = $this->getErrorMessage($error);
			$this->message = "Failed to upload file \"{:field}\": $errorMessage"; // "Не удалось загрузить файл \"{:field}\": $errorMessage"
			return false;
		}
		if ($this->allowedExtensions && !in_array($value->getExtension(), $this->allowedExtensions)) {
			if (count($this->allowedExtensions) === 1) {
				$allowedExtension = '*.'.$this->allowedExtensions[0];
				$this->message = "File \"{:field}\" should have \"$allowedExtension\" extension"; // "Файл \"{:field}\" должен иметь расширение \"$allowedExtension\""
			} else {
				$allowedExtensions = '*.'.implode(', *.', $this->allowedExtensions);
				$this->message = "File \"{:field}\" should have one of supported extensions $allowedExtensions"; // "Файл \"{:field}\" должен иметь одно из допустимых расширений \"$allowedExtensions\""
			}
			return false;
		}
		return true;
	}

}