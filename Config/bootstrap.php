<?php
/**
 * The default language should always be defined
 *
 * If it's not defined, it is assumed to be 'en'
 */
if (!Configure::read('Config.language')) {
	Configure::write('Config.language', 'en');
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
