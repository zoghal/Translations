<?php
App::uses('ExtractBaseTask', 'Translations.Console/Command/Task');
App::uses('Translation', 'Translations.Model');

class ExtractPhpTask extends ExtractBaseTask {

	public function execute() {
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
		$config = Translation::config();
		if ($domain === 'default') {
			$domain = $config['domain'];
		}

		parent::_store($domain, $header, $sentence);
	}
}
