<?php
namespace Pandora3\Form\Fields\FieldRadio;

use Pandora3\Form\Fields\FormField;

/**
 * Class FieldRadio
 * @package Pandora3\Form\Fields\FieldRadio
 */
class FieldRadio extends FormField {

	/**
	 * {@inheritdoc}
	 */
	protected function getValue() {
		if (is_bool($this->value)) {
			return (int) $this->value;
		}
		return $this->value;
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
		if ($options instanceof \Closure) {
			$options = $options();
		}

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