<?php

App::uses('ModelBehavior', 'Model');
App::uses('I18n', 'I18n');
App::uses('I18nModel', 'Model');

/**
 * Translate behavior
 */
class TranslateBehavior extends ModelBehavior {

	public $settings = array();

	protected $_defaultSettings = array(
		'translationKey' => '%s.%s',
		'domain' => 'default',
		'fields' => array()
	);

	protected $_pendingTranslations = array();

	public function setup(Model $Model, $config = array()) {
		if (isset($config[0])) {
			$config = array(
				'fields' => $config
			);
		}

		$config += $this->_defaultSettings;
		foreach ($config['fields'] as &$field) {
			if (!strpos($field, '.')) {
				$field = $Model->alias . '.' . $field;
			}
		}

		$this->settings[$Model->alias] = $config;
	}

/**
 * afterFind Callback
 *
 * @param Model $Model Model find was run on
 * @param array $results Array of model results.
 * @param boolean $primary Did the find originate on $model.
 * @return array Modified results
 */
	public function afterFind(Model $Model, $results, $primary) {
		if (empty($this->settings[$Model->alias]['fields'])) {
			return $results;
		};

		$settings = array(
			'modelAlias' => $Model->alias,
			'modelName' => $Model->name,
			'translationKey' => $this->settings[$Model->alias]['translationKey']
		);
		$iterator = new TranslateInjector($results, $this->settings[$Model->alias]['fields'], $settings);
		$results = iterator_to_array($iterator);
		return $results;
	}

/**
 * beforeSave callback.
 *
 * Prevent updating the actual db value for translated fields
 * Stash updates to a property on this behavior for retrieval in after save
 *
 * @param Model $Model Model save was called on.
 * @return boolean true.
 */
	public function beforeSave(Model $Model, $options = array()) {
		if ($this->id) {
			$locale = Configure::read('Config.language');

			foreach ($this->settings[$Model->alias]['fields'] as $field) {
				list($alias, $field) = explode('.', $field);
				if (isset($Model->data[$Model->alias][$field])) {
					if ($Model->id) {
						$original = $Model->field($field, array($Model->primaryKey => $Model->id));

						$key = sprintf($this->settings[$Model->alias], $Model->name . '.' . $field, $key);
						$value = $Model->data[$Model->alias][$field];
						$this->_pendingTranslations[$locale][$key] = $value;
						unset($Model->data[$Model->alias][$field]);
					}
				}
			}
		}
		return true;
	}

/**
 * afterSave Callback
 *
 * Loop on pending translations, and update translations
 *
 * @param Model $Model Model the callback is called on
 * @param boolean $created Whether or not the save created a record.
 * @return void */
	public function afterSave(Model $Model, $created) {
		if ($this->_pendingTranslations) {
			foreach ($this->_pendingTranslations as $locale => $translations) {
				$params = compact('locale');

				foreach ($translations as $key => $value) {
					Translation::update($key, $value, $params);
				}
			}

			$this->_pendingTranslations = array();
		}
	}

}
