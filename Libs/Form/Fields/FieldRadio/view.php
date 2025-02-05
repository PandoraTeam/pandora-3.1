<?php
/**
 * @var string $name
 * @var mixed|null $value
 * @var string $label
 * @var string $htmlAttribs
 * @var string $fieldHtmlAttribs
 * @var string $inputHtmlAttribs
 * @var array $options
 * @var bool $rawLabels
 * @var bool $wrap
 */
// $disabled = $disabled ?? false;

$renderOptions = static function($options) use ($value, $name, $rawLabels, $inputHtmlAttribs) {
	foreach ($options as $optionValue => $params) {
		if (is_scalar($params)) {
			$params = ['title' => $params];
		}
		$attribs = $params['attribs'] ?? '';
		$title = $params['title'] ?? '';
		$isChecked = ($value === $optionValue); // || ($value === (string) $optionValue);
		echo '<label>';
			echo '<div class="radio-wrap">';
				echo '<input class="radio" type="radio" name="'.$name.'" value="'.$optionValue.'" '.($isChecked ? 'checked' : '').' '.$inputHtmlAttribs.' '.$attribs.'>';
				echo '<i class="radio-icon"></i>';
				echo '<span class="radio-label">';
					echo $rawLabels ? $title : htmlentities($title);
				echo '</span>';
			echo '</div>';
		echo '</label>';
	}
};

if ($wrap) {
	echo '<div '.$fieldHtmlAttribs.'>';
}
		echo '<div class="radio-group">';
			echo '<div '.$htmlAttribs.'>';
				if ($label) {
					echo '<div class="label">'.htmlentities($label).'</div>';
				}
				$renderOptions($options);
			echo '</div>';
		echo '</div>';

if ($wrap) {
	echo '</div>';
}