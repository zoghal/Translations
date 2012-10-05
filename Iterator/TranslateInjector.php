<?php
/**
 * Translate specific array keys in results by passing through Translation::translate
 */
class TranslateInjector extends IteratorIterator {

/**
 * _settings
 *
 * @var array
 */
	protected $_settings = array(
		'domain' => 'default',
		'modelPrimaryKey' => 'id',
		'modelAlias' => null,
		'modelName' => null,
		'fields' => array(),
	);

/**
 * __construct
 *
 * @param array $items
 * @param array $fields
 */
	public function __construct($items = array(), $fields = array(), $settings = array()) {
		if (is_array($items)) {
			$items = new ArrayIterator($items);
		}

		if (!$settings && is_array($fields) && isset($fields['fields'])) {
			$settings = $fields;
		} else {
			$settings['fields'] = (array)$fields;
		}
		$this->_settings = $settings + $this->_settings;

		parent::__construct($items);
	}

/**
 * for each configured field, if it's in the results, translate it
 *
 * @return array
 **/
	public function current() {
		$value = parent::current();

		foreach ($this->_settings['fields'] as $field) {
			$this->_update($value, $field);
		}
		return $value;
	}

/**
 * _update
 *
 * Iterate over the data and translate the specified fields/keys
 *
 * @throws \InternalErrorException if the primary key isn't in the data
 * @param array $data
 * @param string $path
 */
	protected function _update(array &$data, $path) {
		if (empty($data) || empty($path)) {
			return;
		}

		if (empty($data[$this->_settings['modelAlias']][$this->_settings['modelPrimaryKey']])) {
			throw new \InternalErrorException('To use the translate iterator, the primary key value must be present in the data');
		}
		$id = $data[$this->_settings['modelAlias']][$this->_settings['modelPrimaryKey']];

		if (is_string($path)) {
			$parts = explode('.', $path);
		} else {
			$parts = $path;
		}

		$value =& $data;
		foreach ($parts as $key) {
			if (is_array($value) && isset($value[$key])) {
				$value =& $value[$key];
			} else {
				return;
			}
		}
		$value = $this->_translate($path, $value, $id);
	}

/**
 * _translate
 *
 * Translate one field value. If there is no translation at all - return the original
 *
 * @param string $key
 * @param string $value
 * @return string
 */
	protected function _translate($key, $value, $id) {
		$field = preg_replace('@.*\.@', '', $key);
		$key = sprintf('%s.%s.%s', $this->_settings['modelName'], $id, $field);

		$translated = Translation::translate($key, array('domain' => $this->_settings['domain']));
		if ($translated === $key) {
			Translation::update($key, $value, array(
				'locale' => Configure::read('Config.defaultLanguage'),
				'domain' => $this->_settings['domain']
			));
			return $value;
		}
		return $translated;
	}

}
