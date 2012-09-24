<?php
App::uses('TranslationsAppModel', 'Translations.Model');
App::uses('Nodes\L10n', 'Translations.Lib');

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

/**
 * Categories map.
 *
 * used to translate numeric categories (the key) to the string value
 */
	protected static $_categories = array(
		'LC_ALL',
		'LC_COLLATE',
		'LC_CTYPE',
		'LC_MONETARY',
		'LC_NUMERIC',
		'LC_TIME',
		'LC_MESSAGES'
	);

/**
 * Placeholder for the static model instance
 */
	protected static $_model;

	protected static $_locales;

/**
 * Indexed array or all translations
 *
 * root
 * 	<domain name>
 * 		<locale>
 * 			<category>
 *				<translation key>
 */
	protected static $_translations = array();

/**
 * Create a localization based on another (existing) set of translations.
 *
 * @param string $locale   The new locale to create
 * @param mixed  $settings (optional) Set of options or existing locale to fascilitate the creation
 * @return array
 */
	public function createLocale($locale, $settings = array()) {
		// Setup settings
		$defaults = array(
			'basedOn' => Configure::read('Config.language'),
			'nested'  => true
		);
		if (is_string($settings)) {
			$settings = array('basedOn' => $settings);
		}
		$settings = array_merge($defaults, $settings);

		// Validation
		if (empty($locale)) {
			$this->invalidate('locale', 'No locale selected');
			return false;
		}
		if (empty($settings['basedOn'])) {
			$this->invalidate('based_on', 'No base locale selected');
			return false;
		}
		if ($locale == $settings['basedOn']) {
			$this->invalidate('based_on', 'New locale and base locale cannot be the same');
			return false;
		}

		// Save new translations
		$translations = Translation::forLocale($settings['basedOn'], array('nested' => false));
		if (empty($translations)) {
			$this->invalidate('based_on', 'Base locale has no translations');
			return false;
		}
		foreach ($translations as $key => $value) {
			$translation = $this->find('first', array(
				'conditions' => array(
					'locale'         => $locale,
					'key'            => $key
				)
			));
			if (!empty($translation)) {
				continue; // skip it
			}

			$translation = $this->create(array(
				'locale'         => $locale,
				'key'            => $key,
				'value'          => $value
			));
			$this->save($translation);
		}

		// Return complete list
		return Translation::forLocale($locale, $settings);
	}

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

		$settings = $settings + array(
			'nested' => true,
			'addDefaults' => true,
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'section' => null
		);

		$defaultLanguage = Configure::read('Config.language');
		if (!$locale) {
			$locale = $defaultLanguage;
		}

		if ($settings['addDefaults']) {
			$settings['addDefaults'] = false;
			$locales = $this->_fallbackLocales();
			$return = array();
			foreach ($locales as $locale) {
				$return += $this->forLocale($locale, $settings);
			}
			return $return;
		}

		$conditions = array(
			'locale' => $locale,
			'domain' => $settings['domain'],
			'category' => $settings['category']
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
			$l10n = new \Nodes\L10n();
			$locales = $l10n->getLocales();
			self::$_locales = array_map(function($v) {
				return $v['language'];
			}, $locales);
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

/**
 * translate
 *
 * Used by the translation functions in override_i18n in this plugin
 * Returns a translated string based on current locale and translations in the db
 *
 * @param string $singular string to translate
 * @param array $options
 * @return string translated string
 */
	public static function translate($singular, $options = array()) {
		$options += array(
			'plural' => null,
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'count' => null,
			'locale' => !empty($_SESSION['Config']['language']) ? $_SESSION['Config']['language'] : Configure::read('Config.language'),
			'autoPopulate' => Nodes\Environment::isDevelopment()
		);

		$domain = $options['domain'];
		$locale = $options['locale'];
		$category = $options['category'];

		if (is_numeric($category)) {
			$category = self::$_categories[$category];
		}

		if (self::hasTranslation($singular, $options)) {
			return self::$_translations[$domain][$locale][$category][$singular];
		}

		if ($options['autoPopulate']) {
			self::$_translations[$domain][$locale][$category][$singular] = $singular;
			self::$_model->create();
			self::$_model->save(array(
				'domain' => $options['domain'],
				'category' => $options['category'],
				'locale' => $options['locale'],
				'key' => $singular,
				'value' => $singular
			));
		}
		return $singular;
	}

/**
 * update
 *
 * Update one translation
 *
 * @param mixed $key
 * @param array $options
 * @return void
 */
	public static function update($key, $value = '', $options = array()) {
		$defaultLocale = Configure::read('Config.langauge');

		$options += array(
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'locale' => $defaultLocale
		);
		extract($options);

		$update = compact('domain', 'locale', 'category', 'key');
		self::$_translations[$domain][$locale][$category][$key] = $value;
		self::$_model->create();
		self::$_model->id = self::$_model->field('id', $update);
		return self::$_model->save($update + array('value' => $value));
	}

/**
 * reset
 *
 * Reset the static state
 *
 * @return void
 */
	public static function reset() {
		self::$_model = null;
		self::$_translations = null;
	}

/**
 * hasTranslation
 *
 * @param mixed $key
 * @param array $options
 * @return bool
 */
	public function hasTranslation($key, $options = array()) {
		$domain = $options['domain'];
		$category = $options['category'];
		$locale = $options['locale'];

		if (!self::$_model) {
			self::$_model = ClassRegistry::init('Translations.Translation');
		}

		if (empty(self::$_translations[$domain][$locale][$category])) {
			$options['nested'] = false;
			self::$_translations[$domain][$locale][$category] = self::$_model->forLocale($locale, $options);
		}

		if (array_key_exists($key, self::$_translations[$domain][$locale][$category])) {
			return true;
		}
		return false;
	}

/**
 * _fallbackLocales
 *
 * Get the list of locales to itterate through when looking for translations
 *
 * @param mixed $locale
 * @return array
 */
	protected function _fallbackLocales($locale = null) {
		if ($locale) {
			$locales[] = $locale;
		} elseif (!empty($_SESSION['Config']['language'])) {
			$locales[] = $_SESSION['Config']['language'];
		}
		$locales[] = Configure::read('Config.language');
		$locales[] = Configure::read('Config.defaultLanguage');
		$locales = array_unique(array_filter($locales));

		$return = array();
		foreach ($locales as $locale) {
			if (strlen($locale) === 5) {
				$generic = substr($locale, 0, 2);
				if (!in_array($generic, $return)) {
					$return[] = $locale;
					$return[] = substr($locale, 0, 2);
				}
			} else {
				$return[] = $locale;
			}
		}
		return $return;
	}

/**
 * expand dot notation to a nested array
 *
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
				$this->_recursiveInsert($return, $keys, $value);
			} else {
				$return[$key] = $value;
			}
		}
		return $return;
	}

/**
 * _recursiveInsert
 *
 * @param mixed $array
 * @param mixed $keys
 * @param mixed $value
 * @return void
 */
	protected function _recursiveInsert(&$array, $keys, $value) {
		$key = array_shift($keys);
		if (empty($keys)) {
			$array[$key] = $value;
		} else {
			if (!isset($array[$key])) {
				$array[$key] = array();
			}
			$this->_recursiveInsert($array[$key], $keys, $value);
			foreach ($array[$key] as $k => $v) {
				$array[$key][$k] = $v; // array_merge treats string and number keys differently so we have to do it manually
			}
		}
	}
}
