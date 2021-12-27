<?php
/**
 * @var string $name
 * @var string $value
 * @var string $label
 * @var string $htmlAttribs
 * @var string $fieldHtmlAttribs
 * @var bool $wrap
 */

if ($wrap) {
	echo '<div '.$fieldHtmlAttribs.'>';
		echo '<label>';
			if ($label) {
				echo '<span class="label">'.htmlentities($label).'</span>';
			}
}
			echo '<textarea name="'.$name.'" '.$htmlAttribs.'>';
				echo htmlentities($value);
			echo '</textarea>';

if ($wrap) {
		echo '</label>';
	echo '</div>';
}
