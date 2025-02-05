<?php
namespace Pandora3\Form\Fields\FieldSwitch;

use Pandora3\Form\Fields\FormField;

/**
 * Class FieldSwitch
 * @package Pandora3\Form\Fields\FieldSwitch
 */
class FieldSwitch extends FormField {

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