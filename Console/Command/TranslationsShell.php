<?php
App::uses('AppShell', 'Console/Command');

class TranslationsShell extends AppShell {

/**
 * _settings
 *
 * @var array
 */
	protected $_settings = array(
		'domain' => 'default',
		'locale' => 'en',
		'category' => 'LC_MESSAGES'
	);
/**
 * Gets the option parser instance and configures it.
 * By overriding this method you can configure the ConsoleOptionParser before returning it.
 *
 * @return ConsoleOptionParser
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::getOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser
			->addArgument('file', array(
				'help' => 'relative or abs path to translations file',
				'required' => true
			))
			->addOption('locale', array(
				'help' => 'the locale to import/export, defaults to "en"'
			))
			->addOption('domain', array(
				'help' => 'the domain to import/export, defaults to "default"'
			))
			->addOption('category', array(
				'help' => 'the category to import/export, defaults to "LC_MESSAGES"'
			))
			->addSubcommand('import', array(
				'help' => 'Load translations from file',
			));
	}

/**
 * import
 *
 * Load translations in a recognised format.
 * Currently supports:
 * 	php - a file containing $translations => array( key => value)
 *
 * @throws \Exception if the file specified doesn't exist
 */
	public function import() {
		$file = $this->args[0];
		foreach ($this->params as $key => $val) {
			$this->_settings[$key] = $val;
		}

		if (!file_exists($file)) {
			throw new \Exception("File doesn't exist");
		}
		$info = pathinfo($file);
		$parserClass = ucfirst($info['extension']) . 'Parser';
		App::uses($parserClass, 'Translations.Parser');

		$count = 0;
		$return = $parserClass::parse($file, $this->_settings);

		$this->out(sprintf('Found %d translations', $return['count']));
		foreach ($return['translations'] as $domain => $locales) {
			foreach ($locales as $locale => $categories) {
				foreach ($categories as $category => $translations) {
					foreach ($translations as $key => $val) {
						$this->out(sprintf('Processing %s', $key));
						Translation::update($key, $val, compact('domain', 'locale', 'category'));
					}
				}
			}
		}
		$this->out('Done');
	}
}
