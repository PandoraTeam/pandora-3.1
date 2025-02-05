<?php
/**
 * @var string $name
 * @var string $value
 * @var string $htmlAttribs
 */

echo '<input type="hidden" '.$htmlAttribs.' name="'.$name.'" value="'.htmlentities($value).'">';