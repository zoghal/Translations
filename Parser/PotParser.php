<?php
App::uses('PoParser', 'Translations.Parser');

class PotParser extends PoParser {

	public static function parse($file, $defaults = array()) {
		$return = parent::parse($file, $defaults);
		$return['settings']['overwrite'] = false;

		return $return;
	}

}
