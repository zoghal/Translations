<?php
App::uses('ExtractBaseTask', 'Translations.Console/Command/Task');
App::uses('Translation', 'Translations.Model');

/**
 * ExtractPhpTask
 */
class ExtractPhpTask extends ExtractBaseTask {

/**
 * _defaultDomain
 *
 * @var string
 */
	protected $_defaultDomain = 'default';

/**
 * execute
 *
 * Exclude irrelevant files. If it's a vendor or a test file - keep that stuff out of the
 * pot files. also, if it's in the webroot it's either not cake or just the standard text
 * in the index/test.php files which is irrelevant
 *
 * Also set the default domain based on the config for the translations plugin
 *
 * @return void
 */
	public function execute() {
		$config = Translation::config();
		$this->_defaultDomain = $config['domain'];

		$this->params['ignore-model-validation'] = false;

		$this->_exclude[] = 'Test';
		$this->_exclude[] = 'Vendor';
		$this->_exclude[] = 'webroot';

		parent::execute();
	}

/**
 * Prepare a file to be stored
 *
 * Translations plugin allows you to define a default domain that is not "default".
 * If the extract task finds a default-domain translation - redefine it as "whatever".
 * This is important because the filename of a pot file is used as the domain on import.
 *
 * @param string $domain
 * @param string $header
 * @param string $sentence
 * @return void
 */
	protected function _store($domain, $header, $sentence) {
		if ($domain === 'default') {
			$domain = $this->_defaultDomain;
		}

		parent::_store($domain, $header, $sentence);
	}
}
