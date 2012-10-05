<?php
App::uses('TranslationsAppController', 'Translations.Controller');
/**
 * Translations Controller
 */
class TranslationsController extends TranslationsAppController {

	public $helpers = array('Text');

	public $uses = array(
		'Translations.Translation',
	);

	public $paginate = array(
		'order' => 'key ASC',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		if (!empty($this->request->params['prefix']) && $this->request->params['prefix'] === 'admin') {
			$allLocales = Translation::locales(true);
			$locales = Translation::locales();
			$domains = Translation::domains();
			$categories = Translation::categories();
			$this->set(compact('allLocales', 'locales', 'domains', 'categories'));
		}
		$this->Api->allowPublic('flat');
		$this->Api->allowPublic('nested');
		parent::beforeFilter();
	}

/**
 * admin_update_translation
 *
 * Called by the js-based backend edit form

 * See webroot/js/admin.js
 *
 * @return void
 */
	public function admin_update() {
		if (!$this->data) {
			return;
		}
		$data = $this->data['Translation'];
		$return = Translation::update($data['key'], $data['value'], $data);
		$this->set('data', $return);
	}

/**
 * admin_add
 *
 * @param mixed $locale
 * @param string $domain
 * @param string $category
 * @return void
 */
	public function admin_add($locale = null, $domain = 'default', $category = 'LC_MESSAGES') {
		$this->set(compact('locale', 'domain', 'category'));
		$this->Crud->executeAction();
	}

	public function admin_add_locale() {
		$locales = Translation::locales(true);
		$based_on = Translation::locales();

		if ($this->request->is('post') && !empty($this->request->data['Translation']['locale'])) {
			$translations = $this->Translation->createLocale($this->request->data['Translation']['locale'], $this->request->data['Translation']['based_on']);
			if (!empty($translations)) {
				// Go to edit page
				$this->redirect(array(
					'action' => 'edit_locale',
					$this->request->data['Translation']['locale']
				));
			}
		}

		$this->set(compact('locales', 'based_on'));
	}

/**
 * admin_edit_locale
 *
 * @param mixed $locale
 * @param string $domain
 * @param string $category
 * @param mixed $section
 * @return void
 */
	public function admin_edit_locale($locale = null, $domain = 'default', $category = 'LC_MESSAGES', $section = null) {
		$this->set(compact('locale', 'domain', 'category', 'section'));
		if (!$locale) {
			if ($this->data) {
				if (!empty($this->data['Translation']['locale'])) {
					$locale = $this->data['Translation']['locale'];
					return $this->redirect(array($locale, $domain, $category, $section));
				}
			}

			$temp = I18n::getInstance()->l10n->catalog();
			foreach ($temp as $locale => $details) {
				if (strlen($locale) < 2) {
					continue;
				}
				$locale = str_replace('-', '_', $locale);
				$locales[$locale] = $details['language'];
			}
			$this->set('locales', $locales);
			return $this->render('admin_choose_locale');
		}

		if ($this->data) {
			$defaultConditions = compact('locale', 'domain', 'category');
			foreach ($this->data['Translation'] as $key => $value) {
				if ($key === 'id') {
					continue;
				}

				$key = str_replace('¿', '.', $key);

				$conditions = $defaultConditions + compact('key');

				$this->Translation->create();
				$this->Translation->id = $this->Translation->field('id', $conditions);
				$this->Translation->save($conditions + compact('value'));
			}
			$this->Session->setFlash("Translations for $locale updated", 'success');
			return $this->redirect(array('action' => 'index', $locale, $domain, $category, $section));
		}

		$params = compact('domain', 'category', 'section') + array('nested' => false, 'addDefaults' => false);
		$toEdit = $this->Translation->forLocale($locale, $params);

		$defaultLanguage = Configure::read('Config.defaultLanguage');
		if ($defaultLanguage === $locale) {
			$default = $toEdit;
		} else {
			$default = $this->Translation->forLocale($defaultLanguage, $params);
		}

		$this->set(compact('default', 'toEdit'));
		$this->render('admin_edit_locale');
	}

/**
 * admin_index
 *
 * @param mixed $locale
 * @param mixed $domain
 * @param mixed $category
 * @return void
 */
	public function admin_index($locale = null, $domain = 'default', $category = 'LC_MESSAGES') {
		$this->set(compact('locale', 'domain', 'category'));
		$conditions = array('locale' => Configure::read('Config.defaultLanguage')) + compact('domain', 'category');
		if (!empty($this->request->query['all'])) {
			$this->paginate['limit'] = 10000;
		}

		$items = $this->paginate($conditions);
		foreach ($items as &$item) {
			if (preg_match('/^(\w+\.)(\w+\.?)*$/', $item['Translation']['key'])) {
				$item['Translation']['ns'] = current(explode('.', $item['Translation']['key']));
			} else {
				$item['Translation']['ns'] = null;
			}
		}
		$locales = Translation::locales();
		$this->set(compact('items', 'locales'));
	}

/**
 * admin_export
 *
 * @return void
 */
	public function admin_export() {
		if ($this->data) {
			$options = $this->data['Translation'];
			$return = $this->Translation->export(false, $options);

			if ($return['success']) {
				$filename = sprintf("%s-%s-%s.%s", $options['locale'], $options['domain'], $options['category'], $options['format']);
				file_put_contents(TMP . $filename, $return['string']);
				$this->response->file(TMP . $filename, array('download' => true));
			} else {
				$this->Session->setFlash('Errors were generated processing the export', 'error');
			}
		}
	}

/**
 * admin_import
 *
 * @return void
 */
	public function admin_import() {
		if ($this->data) {
			$options = $this->data['Translation'];
			if ($this->Translation->import($this->data['Translation']['import'], $options)) {
				$this->Session->setFlash('Translations imported successfully' . $string, 'success');
				$this->redirect(array(
					'action' => 'index',
					$this->data['Translation']['locale'],
					$this->data['Translation']['domain'],
					$this->data['Translation']['category']
				));
			} else {
				$this->Session->setFlash('Errors were generated processing the import', 'error');
			}
		}
	}

/**
 * flat
 *
 * @param string $locale
 * @param string $domain
 * @param string $category
 * @param string $section
 */
	public function flat($locale = null, $domain = 'default', $category = 'LC_MESSAGES', $section = null) {
		if (!$locale) {
			$locale = Configure::read('Config.language');
		}
		$options = compact('domain', 'category', 'section');
		$options['nested'] = false;
		$return = $this->Translation->forLocale($locale, $options);
		$this->set('items', $return);
		$this->render('index');
	}

/**
 * nested
 *
 * @param string $locale
 * @param string $section
 */
	public function nested($locale = null, $domain = 'default', $category = 'LC_MESSAGES', $section = null) {
		if (!$locale) {
			$locale = Configure::read('Config.language');
		}
		$options = compact('domain', 'category', 'section');
		$return = $this->Translation->forLocale($locale, $options);
		$this->set('items', $return);
		$this->render('index');
	}
}
