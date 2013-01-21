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
