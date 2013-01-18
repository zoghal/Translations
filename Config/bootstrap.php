<?php
/**
 * The default language should always be defined
 *
 * If it's not defined, it is assumed to be 'en'
 */
if (!Configure::read('Config.language')) {
	Configure::write('Config.language', 'en');
}
if (!Configure::read('Config.defaultLanguage')) {
	Configure::write('Config.defaultLanguage', Configure::read('Config.language'));
}

App::uses('Translation', 'Translations.Model');

/**
 * Translation helper function.
 *
 * Intended as a pseudo replacement for cake's own __ function. However in Nodes
 * we prefer the use of placeholders than sprintf format, since none-technical users
 * are the ones defining the translations
 *
 * Any of these sort of calls should work:
 *
 *     t('translate me')
 *     t('welcome back {name}', array('name' => 'John'))
 *     t('welcome back %s, we haven't seen you in %d days', 'John', 123')
 *
 * A break from cake's own funciton, this won't work:
 *     t('welcome back %s, we haven't seen you in %d days', array('John', 123'))
 *
 * @param string $singular Text to translate
 * @param mixed $args Array with arguments or multiple arguments in function
 * @return mixed translated string
 */
function t($singular, $args = null) {
	if (!$singular) {
		return;
	}
	$translated = Translation::translate($singular);

	if (is_null($args)) {
		return $translated;
	} elseif (is_array($args)) {
		if (strpos($translated, '{') !== false) {
			$translated = String::insert($translated, $args, array(
				'before' => '{',
				'after'  => '}'
			));
		}
	} elseif (is_scalar($args)) {
		$args = array_slice(func_get_args(), 1);
		$translated = vsprintf($translated, $args);
	}
	return $translated;
}

// Application configuration for the Translations module
Configure::write('ApplicationConfigurationExtras.translations', array(
	'element'   => 'Translations.application_configuration',
	'modules'   => array('Translations'),
	'global'    => true
));

/**
 * Automatically change the langauge (backend only) if the langauge switch is used
 * Automatically add the language switch to all admin views that have a menu
 */
App::uses('CakeEventManager', 'Event');
CakeEventManager::instance()->attach(
	function ($event) {
		$currentRoute = Router::currentRoute();
		if (empty($currentRoute->defaults['prefix']) || $currentRoute->defaults['prefix'] !== 'admin') {
			return;
		}

		$adminLocale = CakeSession::read('Config.adminLocale');
		if ($adminLocale) {
			Configure::write('Config.actualLocale', Configure::read('Config.language'));
			Configure::write('Config.language', $adminLocale);
		}

		CakeEventManager::instance()->attach(
			function ($event) {
				$event->subject->Html->script('/translations/js/admin.js', array('inline' => false));
			},
			'View.beforeLayout'
		);

		CakeEventManager::instance()->attach(
			function ($event) {
				$content = $event->subject->Blocks->get('content');
				$languageSwitch = $event->subject->element('Translations.language_switch');
				if (!$languageSwitch) {
					return;
				}

				$content = preg_replace(
					'@(<div class="content">\s*<div class="btn-toolbar pull-right">\s*<div class="btn-group single">)@',
					'\1' . $languageSwitch,
					$content
				);

				$event->subject->Blocks->set('content', $content);
			},
			'View.afterLayout'
		);
	},
	'Controller.initialize'
);
