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

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		$defaultLanguage = Configure::read('Config.language');
		if (!$defaultLanguage) {
			$defaultLanguage = 'en';
			Configure::write('Config.language', $defaultLanguage);
		}

		if (!empty($this->request->params['prefix']) && $this->request->params['prefix'] === 'admin') {
			$locales = $this->Translation->find('list', array(
				'fields' => array('locale', 'locale')
			));
			$locales[$defaultLanguage] = $defaultLanguage;
			$this->set('locales', $locales);
		}
		$this->Api->allowPublic('flat');
		$this->Api->allowPublic('nested');
		parent::beforeFilter();
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

		$defaultLanguage = Configure::read('Config.language');
		$params = compact('domain', 'category', 'section') + array('nested' => false);
		$default = $this->Translation->forLocale($defaultLanguage, $params);

		if ($this->data) {
			$defaultConditions = compact('locale', 'domain', 'category', 'section');
			foreach ($this->data['Translation'] as $key => $value) {
				if ($key === 'id') {
					continue;
				}

				$key = str_replace('¿', '.', $key);

				$conditions = $defaultConditions + compact('key');
				if ($locale !== $defaultLanguage && $value === $default[$key]) {
					$this->Translation->deleteAll($conditions);
					continue;
				}

				$this->Translation->create();
				$this->Translation->id = $this->Translation->field('id', $conditions);
				$this->Translation->save($conditions + compact('value'));
			}
			$this->Session->setFlash("Translations for $locale updated", 'success');
			return $this->redirect(array('action' => 'index', $locale, $domain, $category, $section));
		}

		if ($locale !== $defaultLanguage) {
			$params = compact('domain', 'category', 'section') + array('nested' => false);
			$default = $this->Translation->forLocale($defaultLanguage, $params);
		}
		$params = compact('domain', 'category', 'section') + array('nested' => false, 'addDefaults' => false);
		$toEdit = $this->Translation->forLocale($locale, $params);
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
		$conditions = compact('locale', 'domain', 'category');
		$items = $this->paginate($conditions);
		foreach ($items as &$item) {
			if (preg_match('/^(\w+\.?)+$/', $item['Translation']['key'])) {
				$item['Translation']['ns'] = current(explode('.', $item['Translation']['key']));
			} else {
				$item['Translation']['ns'] = null;
			}
		}
		$locales = Translation::locales();
		$this->set(compact('items', 'locales'));
	}

/**
 * admin_upload
 *
 * @return void
 */
	public function admin_upload() {
		if ($this->data) {
			if ($return = $this->Translation->loadPlist(
					$this->data['Translation']['upload']['tmp_name'],
					$this->data['Translation']['locale'],
					array('reset' => $this->data['Translation']['reset']))
				) {

				foreach ($return as $key => &$rows) {
					if (!$rows) {
						unset($return[$key]);
						continue;
					}
					$rows = $key . ": \n\t" . implode($rows, "\n\t") . "\n";
				}
				$string = "<br /><pre>" . implode($return) . "</pre>";
				$this->Session->setFlash('Translations uploaded successfully' . $string, 'success');
			} else {
				$this->Session->setFlash('Errors were generated processing the upload', 'error');
			}
			$this->redirect(array('action' => 'index', $this->data['Translation']['locale']));
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
