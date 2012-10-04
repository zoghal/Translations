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
		'translationKey' => '%s.%s',
		'modelAlias' => null,
		'modelName' => null,
		'fields' => array()
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
		$settings += $this->_settings;

		if ($settings['modelAlias'] && $settings['modelAlias'] === $settings['modelName']) {
			unset ($settings['modelAlias'], $settings['modelName']);
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
 * @param array $data
 * @param string $path
 */
	protected function _update(array &$data, $path) {
		if (empty($data) || empty($path)) {
			return;
		}
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
		$value = $this->_translate($path, $value);
	}

/**
 * _translate
 *
 * Translate one field value. If there is no translation at all - return the original
 *
 * @param string $path
 * @param string $value
 * @return string
 */
	protected function _translate($path, $value) {
		if ($this->_settings['modelAlias']) {
			$path = preg_replace('@^' . $this->_settings['modelAlias'] . '@', $this->_settings['modelName'], $path);
		}
		$key = sprintf($this->_settings['translationKey'], $path, $value);

		$translated = Translation::translate($key, array('domain' => $this->_settings['domain']));
		if ($translated === $key) {
			return $value;
		}
		return $translated;
	}

}
