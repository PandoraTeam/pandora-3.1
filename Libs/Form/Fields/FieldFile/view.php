<?php
/**
 * @var string $name
 * @var string $value
 * @var string $label
 * @var string $htmlAttribs
 * @var string $fieldHtmlAttribs
 * @var bool $wrap
 */

// todo: override template in app
if ($wrap) {
	echo '<div '.$fieldHtmlAttribs.'>';
		echo '<label>';
			if ($label) {
				echo '<span class="label">'.htmlentities($label).'</span>';
			}
}
			echo '<div class="custom-file '.($value ? '' : 'file-empty').'">';
				echo '<span class="file-input-wrap">';
					echo '<input tabindex="-1" type="file" '.$htmlAttribs.' name="'.$name.'">';
				echo '</span>';
				echo '<div role="button" tabindex="0" class="button button-small file-button-select button-secondary"></div>';
				echo '<button onclick="deleteFile(event)" title="Удалить файл" type="button" class="mdi mdi-close file-button-delete"></button>';
				/* if ($value) {
					echo '<div>'.htmlentities($value).'</div>';
				} */
				echo '<span class="file-label-filename">'.htmlentities($value).'</span>';
			echo '</div>';

if ($wrap) {
		echo '</label>';
	echo '</div>';
}