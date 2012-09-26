<?php
class PhpParser {

/**
 * parse
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

/**
 * generate
 *
 * @param mixed $translations
 * @return string
 */
	public static function generate($array = array()) {
		$return = "<?php\n";
		foreach ($array as $var => $value) {
			$exported = var_export($value, true);
			$return .= "\$$var = $exported;\n";
		}
		return $return;
	}

}
