<?php

App::uses('AppShell', 'Console/Command');

class ExtractShell extends AppShell {

/**
 * Contains tasks to load and instantiate
 *
 * @var array
 */
	public $tasks = array(
		'Translations.ExtractPhp',
		'Translations.ExtractJs',
	);

/**
 * Override main() for help message hook
 *
 * @return void
 */
	public function main() {
		$this->php();
		$this->js();
		$this->hr();
	}

	public function php() {
		$this->ExtractPhp->execute();
	}

	public function js() {
		$this->ExtractJs->execute();
	}

/**
 * Get and configure the Option parser
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description(
			'Extract translations for your whole application'
		)->addSubcommand('php', array(
			'help' => 'Extract translations from php source files',
			'parser' => $this->ExtractPhp->getOptionParser()
		))->addSubcommand('js', array(
			'help' => 'Extract translations from js source files',
			'parser' => $this->ExtractJs->getOptionParser()
		));

		return $parser;
	}

}
