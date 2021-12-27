<?php
namespace Pandora3\Form\Fields\FieldCheckboxGroup;

use Pandora3\Form\Fields\FormField;

/**
 * Class FieldCheckboxGroup
 * @package Pandora3\Form\Fields\FieldCheckboxGroup
 */
class FieldCheckboxGroup extends FormField {

	/**
	 * {@inheritdoc}
	 */
	protected function getValue() {
		if ($this->params['flags'] ?? false) {
			if (is_numeric($this->value)) {
				return $this->fromFlags((int) $this->value);
			} else {
				return [];
			}
		}
		return $this->value ?? [];
	}

	protected function fromFlags(int $flags): array {
		$value = [];
		for ($i = 1; $i <= $flags; $i *= 2) {
			if ($i & $flags) {
				$value[] = $i;
			}
		}
		return $value;
	}

	/**
	 * @return array
	 */
	protected function getInputHtmlAttribs(): array {
		return $this->params['inputAttribs'] ?? [];
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getHtmlAttribs(): array {
		return array_replace( parent::getHtmlAttribs(), [
			'disabled' => $this->params['disabled'] ?? false
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getViewParams(): array {
		$params = parent::getViewParams();
		$options = $params['options'] ?? [];

		$inputHtmlAttribs = '';
		foreach ($this->getInputHtmlAttribs() as $key => $value) {
			if (!is_null($value)) {
				$inputHtmlAttribs .= $key.'="'.htmlspecialchars($value).'" ';
			}
		}

		return array_replace([
			'rawLabels' => false,
		], $params, [
			'inputHtmlAttribs' => $inputHtmlAttribs,
			'options' => $options,
		]);
	}

}