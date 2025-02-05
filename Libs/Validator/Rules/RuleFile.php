<?php
namespace Pandora3\Validator\Rules;

use Pandora3\Contracts\UploadedFileInterface;
use Pandora3\Validator\BaseRule;

/**
 * Class RuleFile
 * @package Pandora3\Validator\Rules
 */
class RuleFile extends BaseRule {

	/** @var array|null */
	protected $allowedExtensions;
	
	public const MESSAGE_IS_NOT_A_FILE = 'Field "{:field}" should be a file';
	
	public const MESSAGE_UPLOAD_ERROR_INI_SIZE = 'Failed to upload file "{:field}": File size exceeds limit';
	public const MESSAGE_UPLOAD_ERROR_FORM_SIZE = 'Failed to upload file "{:field}": File size exceeds form limit';
	public const MESSAGE_UPLOAD_ERROR_PARTIAL = 'Failed to upload file "{:field}": File was only partially uploaded';
	public const MESSAGE_UPLOAD_ERROR_NO_FILE = 'Failed to upload file "{:field}": No file was uploaded';
	public const MESSAGE_UPLOAD_ERROR_NO_TMP_DIR = 'Failed to upload file "{:field}": Missing a temporary folder';
	public const MESSAGE_UPLOAD_ERROR_CANT_WRITE = 'Failed to upload file "{:field}": Failed to write file to disk';
	public const MESSAGE_UPLOAD_ERROR_EXTENSION = 'Failed to upload file "{:field}": A PHP extension stopped the file upload';
	public const MESSAGE_UPLOAD_ERROR_UNKNOWN = 'Failed to upload file "{:field}": Unknown error ({:error})';
	
	public const MESSAGE_WRONG_EXTENSION = 'File "{:field}" should have "{:extension}" extension instead of "{:uploadedExtension}"';
	public const MESSAGE_WRONG_EXTENSION_MULTIPLE = 'File "{:field}" should have one of supported extensions "{:extensions}" instead of "{:uploadedExtension}"';
	
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
			UPLOAD_ERR_INI_SIZE => self::MESSAGE_UPLOAD_ERROR_INI_SIZE, // 'File size exceeds limit', // 'Превышен максимальный размер файла ..MB'
			UPLOAD_ERR_FORM_SIZE => self::MESSAGE_UPLOAD_ERROR_FORM_SIZE, // 'File size exceeds form limit', // 'Превышен максимальный размер файла для данной формы ..MB'
			UPLOAD_ERR_PARTIAL => self::MESSAGE_UPLOAD_ERROR_PARTIAL, // 'File was only partially uploaded', // 'Файл был получен только частично'
			UPLOAD_ERR_NO_FILE => self::MESSAGE_UPLOAD_ERROR_NO_FILE, // 'No file was uploaded', // 'Файл не был загружен'
			UPLOAD_ERR_NO_TMP_DIR => self::MESSAGE_UPLOAD_ERROR_NO_TMP_DIR, // 'Missing a temporary folder', // 'Отсутствует временная папка'
			UPLOAD_ERR_CANT_WRITE => self::MESSAGE_UPLOAD_ERROR_CANT_WRITE, // 'Failed to write file to disk', // 'Не удалось записать файл на диск'
			UPLOAD_ERR_EXTENSION => self::MESSAGE_UPLOAD_ERROR_EXTENSION, // 'A PHP extension stopped the file upload', // 'Модуль PHP остановил загрузку файла'
		];
		return $messages[$error] ?? self::MESSAGE_UPLOAD_ERROR_UNKNOWN; // "Unknown error ($error)";
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate($value, array $values = []): bool {
		if (!$value) {
			return true;
		}
		if (!($value instanceof UploadedFileInterface)) {
			// $this->message = 'Field "{:field}" should be a file'; // 'Поле "{:field}" должно быть файлом'
			$this->setMessage(self::MESSAGE_IS_NOT_A_FILE);
			return false;
		}
		$error = $value->getError();
		if ($error === UPLOAD_ERR_NO_FILE) {
			return true;
		}
		if ($error) {
			$errorMessage = $this->getErrorMessage($error);
			// $this->message = "Failed to upload file \"{:field}\": $errorMessage"; // "Не удалось загрузить файл \"{:field}\": $errorMessage"
			$this->setMessage($errorMessage, ['error' => $error]);
			return false;
		}
		$extension = $value->getExtension();
		if ($this->allowedExtensions && !in_array($extension, $this->allowedExtensions)) {
			if (count($this->allowedExtensions) === 1) {
				$allowedExtension = '*.'.$this->allowedExtensions[0];
				// $this->message = "File \"{:field}\" should have \"$allowedExtension\" extension"; // "Файл \"{:field}\" должен иметь расширение \"$allowedExtension\""
				$this->setMessage(self::MESSAGE_WRONG_EXTENSION, ['extension' => $allowedExtension, 'uploadedExtension' => $extension]);
			} else {
				$allowedExtensions = '*.'.implode(', *.', $this->allowedExtensions);
				// $this->message = "File \"{:field}\" should have one of supported extensions \"$allowedExtensions\""; // "Файл \"{:field}\" должен иметь одно из допустимых расширений \"$allowedExtensions\""
				$this->setMessage(self::MESSAGE_WRONG_EXTENSION_MULTIPLE, ['extensions' => $allowedExtensions, 'uploadedExtension' => $extension]);
			}
			return false;
		}
		return true;
	}

}