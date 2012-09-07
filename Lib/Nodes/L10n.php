<?php
namespace Nodes;

/**
 * Localization class for retrieving protected localization data.
 *
 * @package Translations
 * @author  Michael Enger <mien@nodesagency.no>
 */
class L10n extends \L10n {

/**
 * Get a list of available locales.
 *
 * @return array
 */
	public function getLocales() {
		return $this->_l10nCatalog;
 	}

}
