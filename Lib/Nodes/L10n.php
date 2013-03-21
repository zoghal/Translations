<?php
namespace Nodes;

\App::uses('L10n', 'I18n');

/**
 * Localization class for retrieving protected localization data.
 *
 * @package Translations
 * @author  Michael Enger <mien@nodesagency.no>
 */
class L10n extends \L10n {

/**
 * _catalog
 *
 * Post-processed array of locales
 *
 * @var mixed
 */
	protected $_catalog;

/**
 * Get a list of available locales.
 *
 * Strip out single-character langauge domains. These are duplicatad with iso standard
 * domain names:
 * 	e: Greek
 * 	n: Dutch
 * 	p: Polish
 *
 * Also remove the duplicate locale 'koi8-r'
 *
 * @return array
 */
	public function getLocales() {
		if (!$this->_catalog) {
			$this->_catalog = $this->_l10nCatalog;
			unset($this->_catalog['koi8-r']);
			foreach (array_keys($this->_catalog) as $key) {
				if (strlen($key) === 1) {
					unset($this->_catalog[$key]);
				}
			}
		}

		return $this->_catalog;
	}

}
