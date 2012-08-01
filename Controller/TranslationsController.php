<?php
App::uses('TranslationsAppController', 'Translations.Controller');
/**
 * Translations Controller
 */
class TranslationsController extends TranslationsAppController {

	public $uses = array(
		'Translations.Translation',
	);

	public function beforeFilter() {
		if ($this->isAdminRequest()) {
			$locales = $this->Translation->find('list', array(
				'fields' => array('locale', 'locale')
			));
			$locales['en'] = 'en';
			$this->set('locales', $locales);
		}
		$this->Api->allowPublic('flat');
		$this->Api->allowPublic('nested');
		parent::beforeFilter();
	}

	public function admin_edit_locale($locale = null, $section = null) {
		if (!$locale) {
			if ($this->data) {
				if (!empty($this->data['Translation']['locale'])) {
					$locale = $this->data['Translation']['locale'];
					return $this->redirect(array($locale, $section));
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

		$english = $this->Translation->forLocale('en', array('nested' => false, 'section' => $section));

		if ($this->data) {
			foreach ($this->data['Translation'] as $key => $value) {
				if ($key === 'id') {
					continue;
				}

				$key = str_replace('¿', '.', $key);

				if ($locale !== 'en' && $value === $english[$key]) {
					$this->Translation->deleteAll(array(
						'locale' => $locale,
						'key' => $key
					));
					continue;
				}

				$this->Translation->create();
				$this->Translation->id = $this->Translation->field('id', array(
					'locale' => $locale,
					'key' => $key
				));
				$this->Translation->save(array(
					'locale' => $locale,
					'key' => $key,
					'value' => $value
				));
			}
			$this->Session->setFlash("Translations for $locale updated");
			return $this->redirect(array('action' => 'index', $locale, $section));
		}

		if ($locale !== 'en') {
			$english = $this->Translation->forLocale('en', array('nested' => false, 'section' => $section));
		}
		$toEdit = $this->Translation->forLocale($locale, array('nested' => false, 'addDefaults' => false, 'section' => $section));
		$this->set(compact('english', 'toEdit'));
		$this->render('admin_edit_locale');
	}

	public function admin_index($locale = null) {
		$conditions = array();
		if ($locale) {
			$conditions['locale'] = $locale;
		}
		$items = $this->paginate($conditions);
		$locales = $this->Translation->find('list', array(
			'fields' => array('locale', 'locale')
		));
		$this->set(compact('items', 'locales'));
	}

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
				$this->Session->setFlash('Translations uploaded successfully' . $string);
			} else {
				$this->Session->setFlash('Errors were generated processing the upload');
			}
			$this->redirect(array('action' => 'index', $this->data['Translation']['locale']));
		}
	}

/**
 * flat
 *
 * @param string $locale
 * @param string $section
 */
	public function flat($locale = 'en', $section = null) {
		$return = $this->Translation->forLocale($locale, array('nested' => false, 'section' => $section));
		$this->set('items', $return);
		$this->render('index');
	}

/**
 * nested
 *
 * @param string $locale
 * @param string $section
 */
	public function nested($locale = 'en', $section = null) {
		$return = $this->Translation->forLocale($locale, array('section' => $section));
		$this->set('items', $return);
		$this->render('index');
	}
}
