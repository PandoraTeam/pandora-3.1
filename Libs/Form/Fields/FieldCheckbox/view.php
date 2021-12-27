<?php
/**
 * @var string $name
 * @var string $isChecked
 * @var string $label
 * @var string $htmlAttribs
 * @var string $fieldHtmlAttribs
 * @var bool $wrap
 */
// $disabled = $disabled ?? false;

if ($wrap) {
	echo '<div '.$fieldHtmlAttribs.'>';
		echo '<label>';
}
			echo '<div class="checkbox-wrap">'; // '.($disabled ? 'disabled' : '').'
				echo '<input type="hidden" name="'.$name.'" value="0">';
				echo '<input type="checkbox" '.$htmlAttribs.' name="'.$name.'" value="1" '.($isChecked ? 'checked' : '').'>';
				echo '<i class="checkbox-icon"></i>';
				if ($label) {
					echo '<span class="label">'.htmlentities($label).'</span>';
				}
			echo '</div>';

if ($wrap) {
		echo '</label>';
	echo '</div>';
}