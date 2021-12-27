<?php
namespace Pandora3\Form\Fields\FieldText;

use Pandora3\Form\Fields\FormField;

/**
 * Class FieldPassword
 * @package Pandora3\Form\Fields\FieldText
 */
class FieldPassword extends FormField {

	/**
	 * {@inheritdoc}
	 */
	protected function getValue() {
		return (string) $this->value;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getHtmlAttribs(): array {
		return array_replace(
			parent::getHtmlAttribs(), [
				'placeholder' => $this->params['placeholder'] ?? null
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getViewParams(): array {
		return array_replace(
			parent::getViewParams(), [
				'inputType' => 'password',
				'value' => '',
			]
		);
	}

}