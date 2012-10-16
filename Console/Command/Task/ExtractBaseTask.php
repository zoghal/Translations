<?php
App::uses('ExtractTask', 'Console/Command/Task');

class ExtractBaseTask extends ExtractTask {

	public function execute() {
		$this->_getPaths();

		$this->params['extract-core'] = 'no';

		if (!isset($this->params['ignore-model-validation'] )) {
			$this->params['ignore-model-validation'] = true;
		}

		$pluginPaths = App::path('plugins');
		if ($this->_isExtractingApp()) {
			$this->_exclude = array_merge($this->_exclude, $pluginPaths);
		}

		if (!isset($this->params['output'] )) {
			$this->params['output'] = $this->_paths[0] . 'Locale';
		}

		$this->params['merge'] = false;

		$this->params['overwrite'] = true;

		$this->params['paths'] = $this->_paths[0];

		parent::execute();
	}

/**
 * Method to interact with the User and get path selections.
 *
 * @return void
 */
	protected function _getPaths() {
		if (isset($this->params['plugin'])) {
			$this->_paths[] = CakePlugin::path($this->params['plugin']);
		} else {
			$this->_paths[] = APP;
		}
	}
}
