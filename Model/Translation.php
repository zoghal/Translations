<?php
App::uses('TranslationsAppModel', 'Translations.Model');
/**
 * Translation Model
 *
 * @property Application $Application
 */
class Translation extends TranslationsAppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'locale' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			),
		),
		'key' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			),
		)
	);

	protected static $_model;

	protected static $_locales;

	protected static $_translations = array();

/**
 * forLocale
 *
 * @param string $locale
 * @param mixed $addDefaults
 * @return
 */
	public function forLocale($locale = null, $settings = array()) {
		if (!self::$_model) {
			self::$_model = ClassRegistry::init('Translations.Translation');
		}
		if (self::$_model !== $this) {
			return self::$_model->forLocale($locale, $settings);
		}

		$settings = $settings + array('nested' => true, 'addDefaults' => true, 'section' => null);

		$defaultLanguage = Configure::read('Config.language');
		if (!$locale) {
			$locale = $defaultLanguage;
		}

		if ($settings['addDefaults']) {
			$locales = array_unique(array(
				$locale,
				substr($locale, 0, 2),
				$defaultLanguage
			));

			$settings['addDefaults'] = false;
			$return = array();
			foreach ($locales as $locale) {
				$return += $this->forLocale($locale, $settings);
			}
			return $return;
		}

		$conditions = array(
			'locale' => $locale
		);
		if (!empty($settings['section'])) {
			$conditions['key LIKE'] = $settings['section'] . '%';
		}
		$data = $this->find('list', array(
			'fields' => array('key', 'value'),
			'conditions' => $conditions,
			'order' => array('key' => 'ASC')
		));

		if (!$settings['section']) {
			ksort($data);
		}

		if ($settings['nested'] && $data) {
			$data = $this->_expand($data);
			if ($settings['section']) {
				$keys = explode('.', $settings['section']);

				while ($keys) {
					$key = array_shift($keys);
					if (!array_key_exists($key, $data)) {
						$data = array();
						break;
					}
					$data = $data[$key];
				}
			}
		}
		return $data;
	}

/**
 * Lists the avaliable locales.
 *
 * @param boolean $all     (optional) Whether to print out all locales
 * @param array   $options (optional) List of options
 * @return array
 */
	public static function locales($all = false, $options = array()) {
		// Setup options
		$defaults = array(
			'query' => array(
				'fields' => 'Translation.locale',
				'group'  => 'Translation.locale'
			),
			'application' => null
		);
		$options = array_merge($defaults, $options);

		if (!empty($options['application'])) {
			$options['query']['conditions']['Translation.application_id'] = $options['application'];
			$options['query']['bounds'] = false;
		}

		// Load model
		if (!self::$_model) {
			self::$_model = ClassRegistry::init('Translations.Translation');
		}

		// Load languages
		if (!self::$_locales) {
			$json = file_get_contents(dirname(__FILE__) . '/../Config/locales.json');
			self::$_locales = get_object_vars(json_decode($json));
		}

		if ($all) {
			return self::$_locales;
		} else {
			// Get current locales
			$currentLocales = self::$_model->find('all', $options['query']);

			$locales = array();
			foreach ($currentLocales as $locale) {
				$locales[$locale['Translation']['locale']] = self::$_locales[$locale['Translation']['locale']];
			}

			return $locales;
		}
	}

	public static function translate($key, $pluralKey = null, $options = array()) {
		if (is_array($pluralKey)) {
			$options = $pluralKey;
			$pluralKey = null;
		}
		$options += array(
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'count' => null,
			'locale' => Configure::read('Config.language'),
			'autoPopulate' => Nodes\Environment::isDevelopment()
		);

		$locale = $options['locale'];

		if (!self::$_model) {
			self::$_model = ClassRegistry::init('Translations.Translation');
		}
		if (!array_key_exists($locale, self::$_translations)) {
			self::$_translations[$locale] = self::$_model->forLocale($locale, array('nested' => false));
		}

		if (array_key_exists($key, self::$_translations[$locale])) {
			return self::$_translations[$locale][$key];
		}

		if ($options['autoPopulate']) {
			self::$_model->create();
			self::$_model->save(array(
				'locale' => $locale,
				'key' => $key,
				'value' => $key
			));
		}
		return $key;
	}

	public static function clear() {
		self::$_model = null;
		self::$_translations = null;
	}

	public function loadPlist($file, $locale, $options = array()) {
		$doc = new DomDocument();
		if (!$doc->load($file)) {
			throw new \Exception("File could not be loaded");
		}
		$return = array(
			'create' => array(),
			'update' => array(),
			'delete' => array(),
		);

		if (!empty($options['reset'])) {
			$return['delete'] = $this->find('list', array(
				'conditions' => array('locale' => $locale),
				'fields' => array('key', 'key'),
			));
			$this->deleteAll(array('locale' => $locale));
		}

		$array = $this->_parsePlist($doc);
		$parsed = array();
		$this->_flatten($array, $parsed);

		foreach ($parsed as $key => $value) {
			$this->create();
			$this->id = $this->field('id', array(
				'key' => $key,
				'locale' => $locale
			));
			if (!empty($return['delete'][$key])) {
				$return['update'][] = $key;
				unset($return['delete'][$key]);
			} elseif ($this->id) {
				$return['update'][] = $key;
			} else {
				$return['create'][] = $key;
			}

			$this->save(array(
				'key' => $key,
				'value' => $value,
				'locale' => $locale
			));
		}

		return $return;
	}

/**
 * expand dot notation to a nested array
 *
 * TODO move all this plist parsing stuff into a seperate class
 *
 * @throws \Exception if there's too much nesting in a translation key
 * @param mixed $array
 * @return array
 */
	protected function _expand($array) {
		$return = array();
		foreach ($array as $key => $value) {
			if (preg_match('/^(\w+\.?)+$/', $key)) { // for keys of format xxx.yyy.zzz
				$keys = explode('.', $key);
			} else {
				$keys = array($key);
			}

			if (count($keys) > 1) {
				$return = $this->_recursiveInsert($return, $keys, $value);
			} else {
				$return[$key] = $value;
			}
		}
		return $return;
	}

	protected function _recursiveInsert($array, $keys, $value) {
		$key = array_shift($keys);
		if (empty($keys)) {
			$array[$key] = $value;
		} else {
			if (!isset($array[$key])) {
				$array[$key] = array();
			}
			$temp = $this->_recursiveInsert($array[$key], $keys, $value);
			foreach ($temp as $k => $v) {
				$array[$key][$k] = $v; // array_merge treats string and number keys differently so we have to do it manually
			}
		}

		return $array;
	}

	protected function _parseValue( $valueNode ) {
		$valueType = $valueNode->nodeName;

		$transformerName = "_parse_$valueType";

		if ( is_callable(array($this, $transformerName))) {
			// there is a transformer protected function _for this node type
			return call_user_func(array($this, $transformerName), $valueNode);
		}

		// if no transformer was found
		return null;
	}

	protected function _parsePlist( $document ) {
		$plistNode = $document->documentElement;

		$root = $plistNode->firstChild;

		// skip any text nodes before the first value node
		while ( $root->nodeName == "#text" ) {
			$root = $root->nextSibling;
		}

		return $this->_parseValue($root);
	}

	protected function _flatten($array, &$return, $prefix = '') {
		if (!$array) {
			return;
		}
		$keys = array_keys($array);
		foreach ($array as $key => $value) {
			if ($keys[0] === 0) {
				$key += 1;
			}
			if (is_array($value)) {
				$this->_flatten($value, $return, ltrim($prefix . ".$key", '.'));
				continue;
			}
			if ($prefix) {
				$return[$prefix . '.' . $key] = $value;
			} else {
				$return[$key] = $value;
			}
		}
	}

	protected function _parse_integer( $integerNode ) {
		return $integerNode->textContent;
	}

	protected function _parse_string( $stringNode ) {
		return $stringNode->textContent;
	}

	protected function _parse_date( $dateNode ) {
		return $dateNode->textContent;
	}

	protected function _parse_true( $trueNode ) {
		return true;
	}

	protected function _parse_false( $trueNode ) {
		return false;
	}

	protected function _parse_dict( $dictNode ) {
		$dict = array();

		// for each child of this node
		for (
			$node = $dictNode->firstChild;
			$node != null;
			$node = $node->nextSibling
		) {
			if ( $node->nodeName == "key" ) {
				$key = $node->textContent;

				$valueNode = $node->nextSibling;

				// skip text nodes
				while ( $valueNode->nodeType == XML_TEXT_NODE ) {
					$valueNode = $valueNode->nextSibling;
				}

				// recursively parse the children
				$value = $this->_parseValue($valueNode);

				$dict[$key] = $value;
			}
		}

		return $dict;
	}

	protected function _parse_array( $arrayNode ) {
		$array = array();

		for (
			$node = $arrayNode->firstChild;
			$node != null;
			$node = $node->nextSibling
		) {
			if ( $node->nodeType == XML_ELEMENT_NODE ) {
				array_push($array, $this->_parseValue($node));
			}
		}

		return $array;
	}
}
