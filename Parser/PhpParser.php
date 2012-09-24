<?php
class PhpParser {

/**
 * _parsePhp
 *
 * Load a php file, and assume it contains a variable named $translations with a flat list
 * may also define $domain, $locale and $category - these settings would affect
 * all translations in the file
 *
 * @param string $file
 * @param array $defaults
 * @return array
 */
	public static function parse($file, $defaults = array()) {
		extract($defaults);

		$translations = array();
		require $file;

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
}
