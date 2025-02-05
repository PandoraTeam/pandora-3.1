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

// todo: override template in app
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
			echo '<div class="custom-file '.($value ? '' : 'file-empty').'">';
				echo '<input type="hidden" class="file-deleted" name="'.$name.'Delete" value="0">';
				echo '<span class="file-input-wrap">';
					echo '<input tabindex="-1" type="file" '.$htmlAttribs.' name="'.$name.'">';
				echo '</span>';
				// echo '<div role="button" tabindex="0" class="button button-small file-button-select button-secondary"></div>';
				echo '<button type="button" onclick="this.parentNode.click()" class="button button-medium button-secondary file-button-select"></button>';
				echo '<button type="button" title="Удалить файл" class="mdi mdi-close file-button-delete"></button>';
				/* if ($value) {
					echo '<div>'.htmlentities($value).'</div>';
				} */
				echo '<span class="file-label-filename">'.htmlentities($value).'</span>';
			echo '</div>';
			if ($description) {
				echo '<span class="field-description">'.$description.'</span>';
			}

if ($wrap) {
		echo '</label>';
	echo '</div>';
}