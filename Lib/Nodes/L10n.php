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
 * @return array
 */
	public function getLocales() {
		if (!$this->_catalog) {
			$this->_catalog = $this->_l10nCatalog;
			foreach (array_keys($this->_catalog) as $key) {
				if (strlen($key) === 1) {
					unset($this->_catalog[$key]);
				}
			}
		}

		return $this->_catalog;
	}

}
