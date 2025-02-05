<?php
/**
 * @var string $name
 * @var string $value
 * @var string $label
 * @var string $htmlAttribs
 * @var string $fieldHtmlAttribs
 * @var bool $wrap
 * @var string $labelIcon
 * @var string $tooltip
 * @var string $description
 */

if ($wrap) {
	echo '<div '.$fieldHtmlAttribs.'>';
		echo '<label>';
			if ($label) {
				echo '<span class="label">';
					echo $labelIcon.htmlentities($label);
					if ($tooltip) {
						echo '<i class="field-tooltip-icon mdi mdi-help-circle" title="'.$tooltip.'"></i>';
					}
				echo '</span>';
			}
}
			echo '<textarea name="'.$name.'" '.$htmlAttribs.'>';
				echo htmlentities($value);
			echo '</textarea>';
			if ($description) {
				echo '<span class="field-description">'.$description.'</span>';
			}

if ($wrap) {
		echo '</label>';
	echo '</div>';
}
