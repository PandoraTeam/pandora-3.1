<?php
/**
 * @var string $name
 * @var string $value
 * @var string $label
 * @var string $htmlAttribs
 * @var string $fieldHtmlAttribs
 * @var string $inputType
 * @var bool $wrap
 */

if ($wrap) {
	echo '<div '.$fieldHtmlAttribs.'>';
		echo '<label>';
			if ($label) {
				echo '<span class="label">'.htmlentities($label).'</span>';
			}
}
			echo '<input type="'.$inputType.'" '.$htmlAttribs.' name="'.$name.'" value="'.htmlentities($value).'">';

if ($wrap) {
		echo '</label>';
	echo '</div>';
}
