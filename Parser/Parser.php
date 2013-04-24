<?php
App::uses('Parser', 'Translations.Parser');
App::uses('Translation', 'Translations.Model');

abstract class Parser {

/**
 * parse
 *
 * @throws \NotImplementedException if the file cannot be loaded
 * @param string $file
 * @param array $defaults
 * @return array
 */
	public static function parse($file, $defaults = array()) {
		throw new \NotImplementedException(__CLASS__ . "::parse is not been implemented");
	}

/**
 * generate
 *
 * @throws \NotImplementedException if this method is not overriden
 * @param array $array
 * @return string
 */
	public static function generate($array = array()) {
		throw new \NotImplementedException(__CLASS__ . "::generate is not implemented");
	}

/**
 * _parseArray
 *
 * @param array $translations
 * @param array $defaults
 * @return array
 */
	protected static function _parseArray($translations, $defaults) {
		$defaults = array_intersect_key(
			$defaults,
			array_flip(array('domain', 'locale', 'category'))
		);
		$count = 0;
		$return = array();
		foreach ($translations as $key => $value) {
			if (preg_match('/^[a-z]+([A-Z][a-z]+)+$/', $key)) {
				$key = str_replace('_', '.', Inflector::underscore($key));
			}

			if (is_array($value)) {
				foreach ($value as $case => $val) {
					$return[] = $defaults + array(
						'key' => $key,
						'value' => $val,
						'plural_case' => $case
					);
					$count++;
				}
				continue;
			}

			$return[] = $defaults + compact('key', 'value');
			$count++;
		}

		return array(
			'count' => $count,
			'translations' => $return
		);
	}
}
