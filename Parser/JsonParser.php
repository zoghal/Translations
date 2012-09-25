<?php
class JsonParser {

/**
 * parse
 *
 * Load a json file, and assume it contains a flat array of translations
 * OR
 * an array of:
 * 	domain:
 * 	locale
 * 	category:
 * 	translations:
 *
 * @param string $file
 * @param array $defaults
 * @return array
 */
	public static function parse($file, $defaults = array()) {
		extract($defaults);

		$translations = json_decode(file_get_contents($file), true);
		if (isset($translations['translations']) && is_array($translations['translations'])) {
			extract($translations);
		}

		$count = 0;
		$return = array();
		foreach ($translations as $key => $val) {
			if (!strpos($key, '.')) {
				$key = str_replace('_', '.', Inflector::underscore($key));
			}
			$return[$domain][$locale][$category][$key] = $val;
			$count++;
		}

		return array(
			'count' => $count,
			'translations' => $return
		);
	}

/**
 * generate
 *
 * @param mixed $translations
 * @param array $defaults
 * @return string
 */
	public static function generate($translations, $defaults = array()) {
		return json_encode($defaults + array(
			'translations' => $translations
		));
	}
}
