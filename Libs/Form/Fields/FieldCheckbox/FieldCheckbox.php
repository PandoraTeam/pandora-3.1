<?php
namespace Pandora3\Form\Fields\FieldCheckbox;

use Pandora3\Form\Fields\FormField;

/**
 * Class FieldCheckbox
 * @package Pandora3\Form\Fields\FieldCheckbox
 */
class FieldCheckbox extends FormField {

	/**
	 * {@inheritdoc}
	 */
	protected function getValue() {
		return (bool) $this->value;
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

	/**
	 * {@inheritdoc}
	 */
	protected function getViewParams(): array {
		return array_replace(
			parent::getViewParams(), [
				'isChecked' => $this->getValue()
			]
		);
	}

}