<?php
namespace Pandora3\Form\Fields\FieldHidden;

use Pandora3\Form\Fields\FormField;

/**
 * Class FieldHidden
 * @package Pandora3\Form\Fields\FieldHidden
 */
class FieldHidden extends FormField {

	/**
	 * {@inheritdoc}
	 */
	protected function getValue() {
		return (string) $this->value;
	}

}