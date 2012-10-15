<?php
/**
 * TranslationsRemoteSource
 */
class TranslationsRemoteSource extends DataSource {

/**
 * description
 *
 * @var string
 */
	public $description = 'Remote datasource, for using one application as the source of translations in another';

/**
 * read
 *
 * Bespoke read method to read the api of an external translations api
 *
 * @param Model $model
 * @param array $queryData
 * @return void
 */
	public function read(Model $model, $queryData = array()) {
		$class = get_class($model);
		$config = $class::config();

		$url = String::insert($this->config['host'], $queryData['conditions'] + $config);

		$curl = new \Nodes\Curl($url);
		$data = $curl->get()->getResponseBody();

		if (empty($data['success'])) {
			return array();
		}

		$return = array();
		foreach (current($data['data']) as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $case => $val) {
					$return[] = array(
						$model->alias => array(
							'key' => $key,
							'value' => $val,
							'plural_case' => $case
						)
					);
				}
			} else {
				$return[] = array(
					$model->alias => array(
						'key' => $key,
						'value' => $value,
						'plural_case' => 0
					)
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
}
