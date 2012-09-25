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
 * Runtime config settings
 */
	protected static $_config = array(
		'autoPopulate' => null
	);

/**
 * Default config settings
 */
	protected static $_defaultConfig = array(
		'configured' => true,
		'useDbConfig' => 'default',
		'useTable' => 'translations',
		'cacheConfig' => 'default',
		'autoPopulate' => null
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
 * categories
 *
 * Return the list of all categories
 *
 * @return array
 */
	public static function categories() {
		return array_combine(self::$_categories, self::$_categories);
	}

/**
 * Override runtime settings
 *
 * @param array $settings
 * @return array
 */
	public static function config($settings = array()) {
		if (empty($settings) && !empty(self::$_config['configured'])) {
			return self::$_config;
		}

		if (defined('CORE_TEST_CASES')) {
			self::$_defaultConfig['useTable'] = false;
			self::$_defaultConfig['autoPopulate'] = false;
		}

		self::$_config = $settings + self::$_config + self::$_defaultConfig;

		if (is_null(self::$_config['autoPopulate'])) {
			self::$_config['autoPopulate'] = !empty(self::$_config['useTable']);
		}
		return self::$_config;
	}

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
 * domains
 *
 * Return the list of all domains in use
 *
 * @return array
 */
	public static function domains() {
		if (!self::$_model) {
			self::_loadModel();
		}
		$domains = Hash::extract(self::$_model->find('all', array(
			'fields' => array('DISTINCT domain as val')
		)), '{n}.Translation.val');
		return array_combine($domains, $domains);
	}

/**
 * forLocale
 *
 * @param string $locale
 * @param mixed $addDefaults
 * @return
 */
	public static function forLocale($locale = null, $settings = array()) {
		self::config();
		if (!self::$_config['useTable']) {
			return array();
		}
		if (!self::$_model) {
			self::_loadModel();
		}
		return self::$_model->_forLocale($locale, $settings);
	}

/**
 * export
 *
 * @param mixed $file path to create or false to return the string instead
 * @param mixed $settings
 * @return mixed boolean success writing a file - or the string representation
 */
	public static function export($file, $settings = array()) {
		$settings = $settings + array(
			'locale' => Configure::read('Config.language'),
			'nested' => false,
			'addDefaults' => true,
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'format' => null
		);

		if ($settings['format']) {
			$format = $settings['format'];
		} else {
			if ($file) {
				$info = pathinfo($file);
				$format = $info['extension'];
			}
		}
		$settings['translations'] = self::forLocale($settings['locale'], $settings);

		$parserClass = ucfirst($format) . 'Parser';
		App::uses($parserClass, 'Translations.Parser');
		$return = $parserClass::generate($file, $settings);

		if ($file) {
			return file_put_contents($return);
		}
		return $return;
	}

/**
 * import translation definitions
 *
 * @param mixed $file
 * @param mixed $settings
 * @return array
 */
	public static function import($file, $settings = array()) {
		$settings = $settings + array(
			'locale' => Configure::read('Config.language'),
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
		);

		if (!empty($settings['reset'])) {
			if (!self::$_model) {
				self::_loadModel();
			}
			self::$_model->deleteAll(array(
				'locale' => $settings['locale'],
				'domain' => $settings['domain'],
				'category' => $settings['category']
			));
		}

		$return = self::parse($file, $settings);
		foreach ($return['translations'] as $domain => $locales) {
			foreach ($locales as $locale => $categories) {
				foreach ($categories as $category => $translations) {
					foreach ($translations as $key => $val) {
						Translation::update($key, $val, compact('domain', 'locale', 'category'));
					}
				}
			}
		}
		return $return;
	}

/**
 * parse a translations file
 *
 * If $file is an upload, derive from the name the type of file that it is.
 * Look for a parser based on the file extension, and return the output
 *
 * @param mixed $file
 * @param array $settings
 * @return array
 */
	public static function parse($file, $settings = array()) {
		$settings = $settings + array(
			'locale' => Configure::read('Config.language'),
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
		);

		if (is_array($file)) {
			$info = pathinfo($file['name']);
			$file = $file['tmp_name'];
		} else {
			if (!file_exists($file)) {
				throw new \Exception("File doesn't exist");
			}
			$info = pathinfo($file);
		}

		$parserClass = ucfirst($info['extension']) . 'Parser';
		App::uses($parserClass, 'Translations.Parser');
		return $parserClass::parse($file, $settings);
	}

/**
 * Lists the avaliable locales.
 *
 * @param boolean $all     (optional) Whether to print out all locales
 * @param array   $options (optional) List of options
 * @return array
 */
	public static function locales($all = false, $options = array()) {
		self::config();

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

		if (!self::$_model) {
			self::_loadModel();
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
		self::config();
		$options += array(
			'plural' => null,
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'count' => null,
			'locale' => !empty($_SESSION['Config']['language']) ? $_SESSION['Config']['language'] : Configure::read('Config.language'),
			'autoPopulate' => is_null(self::$_config['autoPopulate']) ? Configure::read() : self::$_config['autoPopulate']
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
		self::config();
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
		self::$_config = self::$_defaultConfig;
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
	public static function hasTranslation($key, $options = array()) {
		self::config();
		$domain = $options['domain'];
		$category = $options['category'];
		$locale = $options['locale'];

		if (!self::$_model) {
			self::_loadModel();
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
 * _clearCache
 *
 * Whenever something touches the data increment the cache counter
 *
 * @param mixed $type
 * @return void
 */
	protected function _clearCache($type = null) {
		Cache::increment('translations-counter', self::$_config['cacheConfig']);
		parent::_clearCache();
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
 * _loadModel
 *
 * Load the model instance, using the configured settings
 *
 * @return void
 */
	protected static function _loadModel() {
		self::$_model = ClassRegistry::init(array(
			'class' => 'Translations.Translation',
			'table' => self::$_config['useTable'],
			'ds' => self::$_config['useDbConfig'],
		));
	}

/**
 * _forLocale
 *
 * @param mixed $locale
 * @param mixed $settings
 * @return array
 */
	protected function _forLocale($locale, $settings) {
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

		if (self::$_config['cacheConfig']) {
			$cacheKey = "translations-$locale-{$settings['domain']}-{$settings['category']}{$settings['section']}";
			$counter = (int)Cache::read('translations-counter', self::$_config['cacheConfig']);
			$cacheKey .= "-$counter";

			$cached = Cache::read($cacheKey, self::$_config['cacheConfig']);
			if ($cached !== false) {
				$array = unserialize($cached);
				if (is_array($array)) {
					return $array;
				}
			}
		}

		if ($settings['addDefaults']) {
			$settings['addDefaults'] = false;
			$locales = $this->_fallbackLocales($locale);
			$return = array();
			foreach ($locales as $locale) {
				$return += $this->_forLocale($locale, $settings);
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

		if (!empty($cacheKey)) {
			Cache::write($cacheKey, serialize($data), self::$_config['cacheConfig']);
		}
		return $data;
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
		if (!is_array($array)) {
			return;
		}
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
