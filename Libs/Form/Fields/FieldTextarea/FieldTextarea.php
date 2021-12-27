<?php
namespace Pandora3\Form\Fields\FieldTextarea;

use Pandora3\Form\Fields\FormField;

/**
 * Class FieldTextarea
 * @package Pandora3\Form\Fields\FieldTextarea
 */
class FieldTextarea extends FormField {

	/**
	 * {@inheritdoc}
	 */
	protected function getValue() {
		return (string) $this->value;
	}

}