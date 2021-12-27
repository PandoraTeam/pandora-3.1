<?php
namespace Pandora3\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class JsFilterExtension
 * @package Pandora3\Plugins\Twig
 */
class JsExtension extends AbstractExtension {
	
	/**
	 * {@inheritdoc}
	 */
	public function getFilters() {
		return [
			new TwigFilter('js', function($value) {
				/* if (is_array($value) && empty($value)) {
					return '{}';
				} */
				// todo: escaping inside string literals
				return str_replace('"', '\'', json_encode($value, JSON_UNESCAPED_UNICODE));
			})
		];
	}

}