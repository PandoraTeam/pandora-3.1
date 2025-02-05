<?php
namespace Pandora3\Eloquent;

class SqlFormatter {

	/**
	 * @param string $sql
	 * @return string
	 */
	public static function format(string $sql): string {
		// todo: make better formatting solution
		return str_replace([
			' left join',
			' where',
			' and',
			' or',
			'(',
			')',
		], [
			" \n    ".'left join',
			" \n    ".'where',
			" \n        ".'and',
			" \n        ".'or',
			"(\n        ",
			 "\n    ".')',
		], $sql);
	}

}