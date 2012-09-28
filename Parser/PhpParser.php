<?php
App::uses('Parser', 'Translations.Parser');

class PhpParser extends Parser{

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

		return self::_parseArray($translations, compact('domain', 'locale', 'category') + $defaults);
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
