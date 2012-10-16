<?php
App::uses('ExtractBaseTask', 'Translations.Console/Command/Task');
App::uses('Translation', 'Translations.Model');

class ExtractPhpTask extends ExtractBaseTask {

	protected $_defaultDomain = 'default';

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
 * Account for the default domain being overwritten
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
