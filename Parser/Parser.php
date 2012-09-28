<?php
App::uses('Parser', 'Translations.Parser');

abstract class Parser {

/**
 * parse
 *
 * @throws \Exception if the file cannot be loaded
 * @param string $file
 * @param array $defaults
 * @return array
 */
	public static function parse($file, $defaults = array()) {
		throw new \NotImplementedException(__CLASS__ . "::parse");
	}

/**
 * generate
 *
 * @throws \NotImplementedException if this method is not overriden
 * @param array $array
 * @return string
 */
	public static function generate($array = array()) {
		throw new \NotImplementedException(__CLASS__ . "::generate");
	}

/**
 * _parseArray
 *
 * @param array $translations
 * @param array $defaults
 * @return array
 */
	protected static function _parseArray($translations, $defaults) {
		$count = 0;
		$return = array();
		foreach ($translations as $key => $value) {
			if (!strpos($key, '.')) {
				$key = str_replace('_', '.', Inflector::underscore($key));
			}
			$return[] = compact('domain', 'locale', 'category', 'key', 'value');
			$count++;
		}

		return array(
			'count' => $count,
			'translations' => $return
		);
	}
}
