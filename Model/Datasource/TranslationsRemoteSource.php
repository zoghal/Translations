<?php
App::uses('DboSource', 'Model/Datasource');

/**
 * TranslationsRemoteSource
 *
 * Read translations for a remote location, rather than have a local translations table
 */
class TranslationsRemoteSource extends DboSource {

/**
 * description
 *
 * @var string
 */
	public $description = 'Remote datasource, for using one application as the source of translations in another';

/**
 * __construct
 *
 * We are not a dbo source - we are secretly a datasource and just want the log functions, hence we extend
 * DboSource
 *
 * @param mixed $config
 * @param mixed $autoConnect
 * @return void
 */
	public function __construct($config = null, $autoConnect = true) {
		DataSource::__construct($config);
		$this->fullDebug = Configure::read('debug') > 1;
	}

/**
 * There is only one method which is expected to call this, and it's forLocale
 *
 * It is expected to talk to an api which returns the translations for a single domain/language/category
 *
 * ### Options
 *
 * - log - Whether or not the query should be logged to the memory log.
 *
 * @param string $url to request
 * @param array $options
 * @param array $params values to be bound to the query
 * @return mixed Resource or object representing the result set, or false on failure
 */
	public function execute($url, $options = array(), $params = array()) {
		$options += array('log' => $this->fullDebug);

		$t = microtime(true);

		if (!isset(static::$methodCache[$url])) {
			$this->_result = $this->_curl($url);
			static::$methodCache[$url] = $this->_result;
		} else {
			$this->_result = static::$methodCache[$url];
		}

		if ($options['log']) {
			$this->took = round((microtime(true) - $t) * 1000, 0);
			$this->numRows = $this->affected = $this->_result['data'] ? count(current($this->_result['data'])) : 0;
			$this->logQuery($url, $params);
		}

		if ($this->_result['success']) {
			return $this->_result['data'];
		}
		return false;
	}

/**
 * read
 *
 * Bespoke read method to read the api of an external translations api
 *
 * @param Model $model
 * @param array $queryData
 * @param mixed $recursive
 * @return void
 */
	public function read(Model $model, $queryData = array(), $recursive = null) {
		$class = get_class($model);
		$config = $class::config();

		$url = String::insert($this->config['host'], $queryData['conditions'] + $config);
		$result = $this->execute($url);

		if (!$result) {
			return $result;
		}

		if ($queryData['fields'] === 'COUNT(*) AS count') {
			return count(current($result));
		}

		$defaults = array_intersect_key($queryData['conditions'] + $config, array_flip(array('domain', 'category', 'locale')));

		$return = array();
		foreach (current($result) as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $case => $val) {
					$return[] = array(
						$model->alias => array(
							'key' => $key,
							'value' => $val,
							'plural_case' => $case
						) + $defaults
					);
				}
			} else {
				$return[] = array(
					$model->alias => array(
						'key' => $key,
						'value' => $value,
						'plural_case' => null
					) + $defaults
				);
			}
		}

		return $return;
	}

/**
 * listSources
 *
 * Don't delete, prevents cake from trying to find out what tables are supported
 *
 * @param mixed $data
 * @return void
 */
	public function listSources($data = null) {
		return true;
	}

/**
 * _curl
 *
 * @param mixed $url
 * @return mixed
 */
	protected function _curl($url) {
		$curl = new \Nodes\Curl($url, array(CURLOPT_CONNECTTIMEOUT => 4));
		return $curl->get()->getResponseBody();
	}
}
