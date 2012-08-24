<?php
/**
 * TranslationsShell
 */
class TranslationsShell extends AppShell {

/**
 * main method
 *
 * @throws \Exception for invalid files
 */
	public function import() {
		$file = $this->args[0];
		if (!file_exists($file)) {
			throw new \Exception("File doesn't exist");
		}

		$locale = basename($file, '.plist');
		if (!preg_match('@^[a-z]{2}(_[A-Z]{2})?$@', $locale)) {
			throw new \Exception("$locale is not a valid locale - 'en' and 'en_GB' are two examples of valid locales");
		}

		$this->Translation = ClassRegistry::init('TuborgCoins.Translation');
		$return = $this->Translation->LoadPlist($file, $locale, array('reset' => true));

		foreach ($return as $action => $keys) {
			foreach ($keys as $key) {
				$this->out($action . ' : ' . $key);
			}
		}
	}

/**
 * getOptionParser method
 *
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description(
			'Cli tools to manage db-driven translations'
		)->addSubcommand('import', array(
			'help' => 'Import translations from a plist file'
		));

		return $parser;
	}
}
