<?php
namespace Pandora3\Form\Fields\FieldFile;

use Pandora3\Contracts\FileInterface;
use Pandora3\Contracts\UploadedFileInterface;
use Pandora3\Form\Fields\FormField;

class FieldFile extends FormField {

	/**
	 * {@inheritdoc}
	 */
	protected function getValue() {
		if ($this->value instanceof FileInterface && !($this->value instanceof UploadedFileInterface)) {
			return $this->getDisplayFileName($this->value);
		}
		return '';
	}
	
	/**
	 * @param FileInterface $file
	 * @return string
	 */
	protected function getDisplayFileName(FileInterface $file): string {
		return $file->getName();
	}

	/* *
	 * {@inheritdoc}
	 */
	/* protected function getHtmlAttribs(): array {
		return array_replace(
			parent::getHtmlAttribs(), [
				'placeholder' => $this->params['placeholder'] ?? null
			]
		);
	} */

	/* *
	 * {@inheritdoc}
	 */
	/* protected function getViewParams(): array {
		return array_replace(
			parent::getViewParams(), [
				'inputType' => 'text'
			]
		);
	} */

}