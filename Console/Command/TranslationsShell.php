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
			->addSubcommand('load', array(
					'help' => 'Load translations from file',
					'parser' => array(
						'arguments' => array(
							'file' => array('help' => 'relative or abs path to translations file', 'required' => true)
						)
					)
			));
	}

/**
 * load
 *
 * @return void
 */
	public function load() {
		$file = $this->args[0];

		if (!file_exists($file)) {
			return false;
		}
		$info = pathinfo($file);
		$parser = '_parse' . ucfirst($info['extension']);

		$count = 0;
		$return = $this->$parser($file, $count);

		$this->out(sprintf('Found %d translations', $count));
		foreach ($return as $domain => $locales) {
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

/**
 * _parsePhp
 *
 * Load a php file, and asume it contains a variable named $translations with a flat list
 *
 * @param string $file
 * @param int $count
 * @return array
 */
	protected function _parsePhp($file, &$count) {
		require $file;

		extract($this->_settings);
		$return = array();
		foreach ($translations as $key => $val) {
			$key = str_replace('_', '.', Inflector::underscore($key));
			$return[$domain][$locale][$category][$key] = $val;
			$count++;
		}

		return $return;
	}
}
