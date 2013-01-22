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
