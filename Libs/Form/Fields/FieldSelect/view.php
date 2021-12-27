<?php
/**
 * @var string $name
 * @var mixed|null $value
 * @var string $label
 * @var string $htmlAttribs
 * @var string $fieldHtmlAttribs
 * @var array $options
 * @var bool $groupOptions
 * @var bool $wrap
 */

$renderOptions = function($options) use ($value) {
	foreach ($options as $optionValue => $params) {
		if (is_scalar($params)) {
			$params = ['title' => $params];
		}
		$attribs = $params['attribs'] ?? '';
		$title = $params['title'] ?? '';
		$isSelected = ($value === $optionValue); // || ($value === (string) $optionValue);
		echo '<option value="'.$optionValue.'" '.(($isSelected) ? 'selected' : '').' '.$attribs.'>';
			echo htmlentities($title);
		echo '</option>';
	}
};

if ($wrap) {
	echo '<div '.$fieldHtmlAttribs.'>';
		echo '<label>';
			if ($label) {
				echo '<span class="label">'.htmlentities($label).'</span>';
			}
}
			echo '<div class="select-wrap">';
				echo '<select name="'.$name.'" '.$htmlAttribs.'>';
					if ($groupOptions && $options) {
						foreach ($options as $groupTitle => $subOptions):
							if ($groupTitle === ':root') {
								$renderOptions($subOptions);
							} else {
								echo '<optgroup label="'.htmlentities($groupTitle).'">';
									$renderOptions($subOptions);
								echo '</optgroup>';
							}
						endforeach;
					} else {
						echo $renderOptions($options);
					}
				echo '</select><div class="custom-select"></div>';
			echo '</div>';

if ($wrap) {
		echo '</label>';
	echo '</div>';
}