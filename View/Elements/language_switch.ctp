<?php
$locale = Configure::read('Config.language');
$locales = Translation::locales();

$domains = Translation::domains();

/**
 * For all admin views, if there's a data domain (the translate behavior is being used) and more than
 * one locale - enable the language switch.
 * Enable for All translation plugin views
 */
$showLanguageSwitch = (
	(!empty($domains['data']) && count($locales) > 1) ||
	($this->name === 'Translations')
);
/**
 * Only for edit domain and the index, add a domain switch
 */
$showDomainSwitch = ($this->name === 'Translations' && in_array($this->action, array('admin_edit_domain', 'admin_index')));

if ($showLanguageSwitch) {
	$url = null;
	if ($this->name === 'Translations' && in_array($this->action, array('admin_edit_locale', 'admin_index'))) {
		$url = Router::url(array('_locale_', $domain));
	}
	echo $this->Form->select('locale', $locales, array(
		'empty' => __d('translations', 'change locale'),
		'id' => 'localeChange',
		'data-ping-url' => Router::url(array('plugin' => 'translations', 'controller' => 'translations', 'action' => 'set_locale', 'ext' => 'json')),
		'data-reload-url' => $url,
		'value' => $locale,
		'class' => 'btn'
	));
}

if ($showDomainSwitch) {
	unset($domains['data']);
	if (count($domains) > 1) {
		$url = Router::url(array($locale, '_domain_'));
		echo $this->Form->select('domain', $domains, array(
				'empty' => __d('translations', 'change domain'),
				'id' => 'domainChange',
				'data-reload-url' => $url,
				'value' => $domain,
				'class' => 'btn'
		));
	}
}
