<?php
namespace Pandora3\Form\Fields\FieldSelect;

use Pandora3\Form\Fields\FormField;

/**
 * Class FieldSelect
 * @package Pandora3\Form\Fields\FieldSelect
 */
class FieldSelect extends FormField {

	/**
	 * {@inheritdoc}
	 */
	protected function getViewParams(): array {
		$params = parent::getViewParams();
		$options = $params['options'] ?? [];
		if ($options instanceof \Closure) {
			$options = $options();
		}
		return array_replace($params, [
			'options' => $options,
			'groupOptions' => $this->isGroupOptions($options),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getHtmlAttribs(): array {
		return array_replace( parent::getHtmlAttribs(), [
			'disabled' => $this->params['disabled'] ?? false
		]);
	}

	protected function isGroupOptions($options) {
		$firstValue = array_values($options)[0] ?? null;
		return (
			!is_null($firstValue) &&
			!is_scalar($firstValue) &&
			!array_key_exists('title', $firstValue)
		);
	}

}