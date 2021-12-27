<?php
namespace Pandora3\Cli;

use Pandora3\Console\ConsoleApplication;

/**
 * Class Cli
 * @package Pandora3\Cli
 */
class Cli extends ConsoleApplication {

	public function run() {
		$logo = file_get_contents(__DIR__ . '/assets/logo');
		// $this->output->write($logo."\n");
		echo $logo."\n";
	}

}