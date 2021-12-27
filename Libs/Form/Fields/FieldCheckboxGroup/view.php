<?php
/**
 * @var string $name
 * @var array $value
 * @var string $label
 * @var string $htmlAttribs
 * @var string $fieldHtmlAttribs
 * @var string $inputHtmlAttribs
 * @var array $options
 * @var bool $rawLabels
 * @var bool $wrap
 */
// $disabled = $disabled ?? false;

$renderOptions = function($options) use ($value, $name, $rawLabels, $inputHtmlAttribs) {
	foreach ($options as $optionValue => $params) {
		if (is_scalar($params)) {
			$params = ['title' => $params];
		}
		$attribs = $params['attribs'] ?? '';
		$title = $params['title'] ?? '';
		$isChecked = in_array($optionValue.'', $value);
		echo '<label>';
			echo '<div class="checkbox-wrap">';
				echo '<input class="checkbox" type="checkbox" name="'.$name.'[]" value="'.$optionValue.'" '.($isChecked ? 'checked' : '').' '.$inputHtmlAttribs.' '.$attribs.'>';
				echo '<i class="checkbox-icon"></i>';
				echo '<span class="label">';
					echo $rawLabels ? $title : htmlentities($title);
				echo '</span>';
			echo '</div>';
		echo '</label>';
	}
};

if ($wrap) {
	echo '<div '.$fieldHtmlAttribs.'>';
}
		echo '<div class="checkbox-group">';
			echo '<div '.$htmlAttribs.'>';
				if ($label) {
					echo '<span class="label">'.htmlentities($label).'</span>';
				}
				echo $renderOptions($options);
			echo '</div>';
		echo '</div>';

if ($wrap) {
	echo '</div>';
}