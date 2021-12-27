<?php
namespace Pandora3\Form\Fields\FieldDate;

use Pandora3\Form\Fields\FormField;

/**
 * Class FieldCalendar
 * @package Pandora3\Form\Fields\FieldDate
 */
class FieldCalendar extends FormField {

	/**
	 * {@inheritdoc}
	 */
	protected function getValue() {
		if ($this->value instanceof \DateTimeInterface) {
			$format = $this->params['format'] ?? 'd.m.Y';
			return $this->value->format($format);
		}
		return (string) $this->value;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getHtmlAttribs(): array {
		return array_replace(
			parent::getHtmlAttribs(), [
				'placeholder' => $this->params['placeholder'] ?? null,
				'disabled' => $this->params['disabled'] ?? false,
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getViewParams(): array {
		return array_replace(
			parent::getViewParams(), [
				'inputType' => 'date'
			]
		);
	}

}