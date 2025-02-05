<?php
namespace Pandora3\Form\Fields\FieldFile;

use Pandora3\Contracts\UploadedFileInterface;
use Pandora3\Contracts\UploadFileNameInterface;
use Pandora3\Form\Fields\FormField;

class FieldFile extends FormField {

	/**
	 * {@inheritdoc}
	 */
	protected function getValue() {
		if ($this->value instanceof UploadFileNameInterface && !($this->value instanceof UploadedFileInterface)) {
			return $this->value->getUploadName();
		}
		return '';
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getHtmlAttribs(): array {
		$extensions = $this->params['extensions'] ?? null;
		if ($extensions) {
			$extensions = implode(',', $extensions);
		}
		return array_replace(
			parent::getHtmlAttribs(), [
				'accept' => $extensions,
			]
		);
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