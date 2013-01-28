<?php
$locale = Configure::read('Config.language');
$locales = Translation::locales();

if (count($locales) > 1) {
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

if ($this->name === 'Translations' && in_array($this->action, array('admin_edit_domain', 'admin_index'))) {
	$domains = Translation::domains();
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
